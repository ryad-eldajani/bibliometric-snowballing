<?php
/*
 * This file is part of the Bibliometric Snowballing project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace BS\Controller;


use BS\Model\Api\AbstractApi;
use BS\Model\Api\CrossRefApi;
use BS\Model\Entity\Author;
use BS\Model\Entity\Journal;
use BS\Model\Entity\Project;
use BS\Model\Entity\Work;
use BS\Helper\ValidatorHelper;
use BS\Model\Http\Response;
use BS\Model\Http\JsonResponse;

class WorkController extends AbstractController
{
    /**
     * URL: /works/view/{workId}
     * Methods: GET
     * @param array $params variable URL params
     * @return Response instance
     */
    public function viewWorkAction(array $params = array())
    {
        $this->redirectIfNotLoggedIn();
        $work = Work::read($params['workId']);
        $work->getAuthors();
        $work->getJournals();

        return new Response(
            $this->app->renderTemplate(
                'work',
                array(
                    'dataTable' => true,
                    'work' => $work,
                    'projects' => Project::read()
                )
            )
        );
    }

    /**
     * URL: /works/request/doi
     * Methods: POST
     * @return JsonResponse instance
     */
    public function requestDoiWorkAction()
    {
        $this->errorJsonResponseIfNotLoggedIn();

        // If HTTP method is not POST, send bad request response.
        if (!$this->http->getRequestInfo('request_method') == 'post') {
            return new JsonResponse(
                array('error' => 'Wrong request.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Validate Ajax request.
        $validationInfo = array(
            'work_doi' => array(
                'type' => 'string',
                'required' => true,
                'min' => 2,
                'max' => 250
            ),
        );

        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Ajax request is validated, create project entity in database.
        $work = Work::readByDoi($this->http->getPostParam('work_doi'));

        return new JsonResponse($work);
    }

    /**
     * URL: /works/assign
     * Methods: POST
     * @return JsonResponse instance
     */
    public function assignWorksAction()
    {
        $this->errorJsonResponseIfNotLoggedIn();

        // If HTTP method is not POST, send bad request response.
        if (!$this->http->getRequestInfo('request_method') == 'post') {
            return new JsonResponse(
                array('error' => 'Wrong request.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Validate Ajax request.
        $validationInfo = array(
            'project_id' => array(
                'required' => true,
                'type' => 'int'
            ),
            'work_ids' => array(
                'type' => 'array',
                'structure' => array(
                    'work_id' => array(
                        'type' => 'int'
                    ),
                )
            )
        );

        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        $project = Project::read($this->http->getPostParam('project_id'));

        // If project is not existent/user not owner of this project.
        if ($project === null) {
            return new JsonResponse(
                array('error' => 'Project unknown.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        $allWorks = array();
        foreach ($this->http->getPostParam('work_ids') as $workId) {
            $work = Work::read($workId['work_id']);
            if (
                !$project->hasWorkId($work->getId())
                && !in_array((string)$work->getId(), array_keys($allWorks))
            ) {
                $allWorks[(string)$work->getId()] = $work->toArray();
                $project->addWorkId($work->getId());
            }
        }
        
        if (count($allWorks) > 0) {
            $project->update();
        }

        return new JsonResponse($allWorks);
    }

    /**
     * URL: /works/new
     * Methods: POST
     * @return JsonResponse instance
     */
    public function newWorkAction()
    {
        $this->errorJsonResponseIfNotLoggedIn();

        // If HTTP method is not POST, send bad request response.
        if (!$this->http->getRequestInfo('request_method') == 'post') {
            return new JsonResponse(
                array('error' => 'Wrong request.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Validate Ajax request.
        $validationInfo = array(
            'project_id' => array(
                'required' => true,
                'type' => 'int'
            ),
            'work_title' => array(
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 250
            ),
            'work_subtitle' => array(
                'type' => 'string',
                'max' => 250
            ),
            'work_year' => array(
                'type' => 'int',
                'min' => 1500,
                'max' => 2200
            ),
            'work_doi' => array(
                'type' => 'string',
                'max' => 250
            ),
            'journals' => array(
                'type' => 'array',
                'structure' => array(
                    'id' => array(
                        'type' => 'int'
                    ),
                    'journal_name' => array(
                        'type' => 'string'
                    ),
                    'issn' => array(
                        'type' => 'string'
                    )
                )
            ),
            'authors' => array(
                'type' => 'array',
                'structure' => array(
                    'id' => array(
                        'type' => 'int'
                    ),
                    'first_name' => array(
                        'type' => 'string'
                    ),
                    'last_name' => array(
                        'type' => 'string'
                    )
                )
            )
        );
        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Ajax request is validated, read or create project entity in database.
        $work = null;
        if ($this->http->hasPostParam('work_doi')) {
            $work = Work::readByDoi($this->http->getPostParam('work_doi'));
        }

        if ($work === null) {
            $work = new Work(
                null,
                $this->http->getPostParam('work_title'),
                $this->http->getPostParam('work_subtitle'),
                $this->http->getPostParam('work_year'),
                $this->http->getPostParam('work_doi')
            );
            $work->create();
        }

        $authors = $this->http->getPostParam('authors');
        if (is_array($authors)) {
            foreach ($authors as $author) {
                if (isset($author['id'])) {
                    $work->addAuthorId($author['id']);
                } else {
                    // Check, if we find the author by name, otherwise we need to create an author.
                    $newAuthor = Author::readByFirstLastName($author['first_name'], $author['last_name']);
                    if ($newAuthor == null) {
                        $newAuthor = new Author(null, $author['first_name'], $author['last_name']);
                        $newAuthor->create();
                    }

                    $work->addAuthorId($newAuthor->getId());
                }
            }
        }

        $journals = $this->http->getPostParam('journals');
        if (is_array($journals)) {
            foreach ($journals as $journal) {
                if (isset($journal['id'])) {
                    $work->addJournalId($journal['id']);
                } else {
                    // Check, if we find the journal by ISSN, otherwise we need to create a journal.
                    $newJournal = Journal::readByIssn($journal['issn']);
                    if ($newJournal == null) {
                        $newJournal = new Journal(null, $journal['journal_name'], $journal['issn']);
                        $newJournal->create();
                    }

                    $work->addJournalId($newJournal->getId());
                }
            }
        }

        $project = Project::read($this->http->getPostParam('project_id'));
        $project->addWorkId($work->getId());
        $project->update();

        return new JsonResponse($work);
    }

    /**
     * URL: /works/request/references
     * Methods: POST
     * @return JsonResponse instance
     */
    public function requestDoiReferencesAction()
    {
        $this->errorJsonResponseIfNotLoggedIn();

        // If HTTP method is not POST, send bad request response.
        if (!$this->http->getRequestInfo('request_method') == 'post') {
            return new JsonResponse(
                array('error' => 'Wrong request.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Validate Ajax request.
        $validationInfo = array(
            'work_ids' => array(
                'type' => 'array',
                'structure' => array(
                    'work_id' => array(
                        'type' => 'int'
                    ),
                )
            )
        );

        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        $allReferencedWorks = array();
        foreach ($this->http->getPostParam('work_ids') as $workId) {
            $work = Work::read($workId['work_id']);

            /** @var CrossRefApi $api */
            $api = AbstractApi::instance('crossref');
            $workData = $api->getDoiInformation($work->getDoi());

            if ($workData === null) {
                continue;
            }

            if (isset($workData['reference'])) {
                foreach ($workData['reference'] as $reference) {
                    $referencedDoi = isset($reference['DOI']) ? trim($reference['DOI']) : '';
                    if ($referencedDoi != '') {
                        if (!in_array($referencedDoi, $allReferencedWorks)) {
                            $allReferencedWorks[$referencedDoi] = 1;
                        } else {
                            $allReferencedWorks[$referencedDoi]++;
                        }
                    }
                }
            }
        }

        return new JsonResponse($allReferencedWorks);
    }
}
