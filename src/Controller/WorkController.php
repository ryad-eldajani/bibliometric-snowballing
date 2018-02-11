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
        $this->wrongJsonResponseIfNotPost();
        $this->validateAjax(
            array(
                'work_doi' => array(
                    'type' => 'string',
                    'required' => true,
                    'min' => 2,
                    'max' => 250
                ),
            )
        );

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
        $this->wrongJsonResponseIfNotPost();
        $this->validateAjax(
            array(
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
            )
        );

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
                $allWorks[(string)$work->getId()]['created_at'] = time();
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
        $this->wrongJsonResponseIfNotPost();
        $this->validateAjax(
            array(
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
                            'type' => 'string',
                            'min' => 1,
                            'max' => 45
                        ),
                        'issn' => array(
                            'type' => 'string',
                            'min' => 0,
                            'max' => 250
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
                            'type' => 'string',
                            'min' => 1,
                            'max' => 250
                        ),
                        'last_name' => array(
                            'type' => 'string',
                            'min' => 1,
                            'max' => 250
                        )
                    )
                )
            )
        );

        $project = Project::read($this->http->getPostParam('project_id'));
        if ($project === null) {
            return new JsonResponse(
                array('error' => 'Project unknown.'),
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

        if ($project->hasWork($work)) {
            return new JsonResponse(
                array('error' => 'Work already assigned.'),
                Response::HTTP_STATUS_NO_CONTENT
            );
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
        $this->wrongJsonResponseIfNotPost();
        $this->validateAjax(
            array(
                'work_ids' => array(
                    'type' => 'array',
                    'structure' => array(
                        'work_id' => array(
                            'type' => 'int'
                        ),
                    )
                )
            )
        );

        $project = Project::read($this->http->getPostParam('project_id'));
        if (!$project instanceof Project) {
            return new JsonResponse(
                array('error' => 'Project unknown.'),
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
                    $referenceDoi = isset($reference['DOI']) ? trim($reference['DOI']) : '';
                    if ($referenceDoi != '') {
                        // If the current project already has this work, continue.
                        if ($project->hasWorkWithDoi($referenceDoi)) {
                            continue;
                        }

                        if (!in_array($referenceDoi, $allReferencedWorks)) {
                            $allReferencedWorks[$referenceDoi] = 1;
                            $work->insertDoiReference($referenceDoi);
                        } else {
                            $allReferencedWorks[$referenceDoi]++;
                        }
                    }
                }
            }
        }

        return new JsonResponse($allReferencedWorks);
    }

    /**
     * Returns a work entity for an Ajax request.
     *
     * @return Work|null work entity or null
     */
    protected function getWorkFromAjaxRequest()
    {
        $this->errorJsonResponseIfNotLoggedIn();
        $this->wrongJsonResponseIfNotPost();
        $this->validateAjax(
            array(
                'work_id' => array(
                    'type' => 'int',
                    'required' => true,
                    'min' => 1
                )
            )
        );

        $work = Work::read($this->http->getPostParam('work_id'));
        if ($work === null) {
            (new JsonResponse(
                array('error' => 'Work not available.'),
                Response::HTTP_STATUS_BAD_REQUEST
            ))->send();
        }

        return $work;
    }

    /**
     * Returns an author entity for an Ajax request.
     *
     * @param bool $createAuthor if true, author is created if not available
     * @return Author|null author entity or null
     */
    protected function getAuthorFromAjaxRequest($createAuthor = true)
    {
        $this->errorJsonResponseIfNotLoggedIn();
        $this->wrongJsonResponseIfNotPost();
        $author = null;

        if ($createAuthor) {
            // Try to load author by first-/last-name, if not available, create one.
            $this->validateAjax(
                array(
                    'first_name' => array(
                        'type' => 'string',
                        'min' => 1,
                        'max' => 250
                    ),
                    'last_name' => array(
                        'type' => 'string',
                        'min' => 1,
                        'max' => 250
                    )
                )
            );

            $author = Author::readByFirstLastName(
                $this->http->getPostParam('first_name'),
                $this->http->getPostParam('last_name')
            );

            if ($author === null) {
                $author = new Author(
                    null,
                    $this->http->getPostParam('first_name'),
                    $this->http->getPostParam('last_name')
                );
                $author->create();
            }
        } else {
            // Try to load author author_id.
            $this->validateAjax(
                array(
                    'author_id' => array(
                        'type' => 'int',
                        'min' => 1
                    ),
                )
            );

            $author = Author::read($this->http->getPostParam('author_id'));
        }

        // If no author is found/created, send JsonResponse error.
        if ($author === null) {
            (new JsonResponse(
                array('error' => 'Author not available.'),
                Response::HTTP_STATUS_BAD_REQUEST
            ))->send();
        }

        return $author;
    }

    /**
     * Returns a journal entity for an Ajax request.
     *
     * @param bool $createJournal if true, journal is created if not available
     * @return Journal|null journal entity or null
     */
    protected function getJournalFromAjaxRequest($createJournal = true)
    {
        $this->errorJsonResponseIfNotLoggedIn();
        $this->wrongJsonResponseIfNotPost();
        $journal = null;

        if ($createJournal) {
            // Try to load author by first-/last-name, if not available, create one.
            $this->validateAjax(
                array(
                    'journal_name' => array(
                        'type' => 'string',
                        'min' => 1,
                        'max' => 45
                    ),
                    'issn' => array(
                        'type' => 'string',
                        'min' => 0,
                        'max' => 250
                    )
                )
            );

            $journal = Journal::readByIssn($this->http->getPostParam('issn'));

            if ($journal === null) {
                $journal = new Journal(
                    null,
                    $this->http->getPostParam('journal_name'),
                    $this->http->getPostParam('issn')
                );
                $journal->create();
            }
        } else {
            // Try to load author author_id.
            $this->validateAjax(
                array(
                    'journal_id' => array(
                        'type' => 'int',
                        'min' => 1
                    ),
                )
            );

            $journal = Journal::read($this->http->getPostParam('journal_id'));
        }

        // If no author is found/created, send JsonResponse error.
        if ($journal === null) {
            (new JsonResponse(
                array('error' => 'Journal not available.'),
                Response::HTTP_STATUS_BAD_REQUEST
            ))->send();
        }

        return $journal;
    }

    /**
     * URL: /works/update
     * Methods: POST
     * @return JsonResponse instance
     */
    public function updateWorkAction()
    {
        $this->errorJsonResponseIfNotLoggedIn();
        $this->wrongJsonResponseIfNotPost();
        $this->validateAjax(
            array(
                'work_id' => array(
                    'type' => 'int',
                    'min' => 1
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
                )
            )
        );

        $work = Work::read($this->http->getPostParam('work_id'));

        if ($work === null) {
            return new JsonResponse(
                array('error' => 'Work not available.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }
        $work->setTitle($this->http->getPostParam('work_title'));
        $work->setSubTitle($this->http->getPostParam('work_subtitle'));
        $work->setWorkYear($this->http->getPostParam('work_year'));
        $work->setDoi($this->http->getPostParam('work_doi'));
        $work->update();

        return new JsonResponse(true);
    }

    /**
     * URL: /works/doi/add
     * Methods: POST
     * @return JsonResponse instance
     */
    public function addWorkDoiAction()
    {
        $this->validateAjax(
            array(
                'work_doi' => array(
                    'type' => 'string',
                    'required' => true,
                    'min' => 2,
                    'max' => 250
                )
            )
        );
        $work = $this->getWorkFromAjaxRequest();
        if (!$work->addDoiReference($this->http->getPostParam('work_doi'))) {
            return new JsonResponse(
                array('error' => 'Adding DOI failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        return new JsonResponse(true);
    }

    /**
     * URL: /works/doi/delete
     * Methods: POST
     * @return JsonResponse instance
     */
    public function deleteWorkDoiAction()
    {
        $this->validateAjax(
            array(
                'work_doi' => array(
                    'type' => 'string',
                    'required' => true,
                    'min' => 2,
                    'max' => 250
                )
            )
        );
        $work = $this->getWorkFromAjaxRequest();
        $work->removeDoiReference($this->http->getPostParam('work_doi'));

        return new JsonResponse(true);
    }

    /**
     * URL: /works/author/add
     * Methods: POST
     * @return JsonResponse instance
     */
    public function addWorkAuthorAction()
    {
        $author = $this->getAuthorFromAjaxRequest(true);
        $work = $this->getWorkFromAjaxRequest();
        if (!$work->addAuthor($author)) {
            return new JsonResponse(
                array('error' => 'Adding author failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        return new JsonResponse($author);
    }

    /**
     * URL: /works/author/delete
     * Methods: POST
     * @return JsonResponse instance
     */
    public function deleteWorkAuthorAction()
    {
        $author = $this->getAuthorFromAjaxRequest(false);
        $work = $this->getWorkFromAjaxRequest();
        $work->removeAuthor($author);

        return new JsonResponse(true);
    }

    /**
     * URL: /works/journal/add
     * Methods: POST
     * @return JsonResponse instance
     */
    public function addWorkJournalAction()
    {
        $journal = $this->getJournalFromAjaxRequest(true);
        $work = $this->getWorkFromAjaxRequest();
        if (!$work->addJournal($journal)) {
            return new JsonResponse(
                array('error' => 'Adding journal failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        return new JsonResponse($journal);
    }

    /**
     * URL: /works/journal/delete
     * Methods: POST
     * @return JsonResponse instance
     */
    public function deleteWorkJournalAction()
    {
        $journal = $this->getJournalFromAjaxRequest(false);
        $work = $this->getWorkFromAjaxRequest();
        $work->removeJournal($journal);

        return new JsonResponse(true);
    }
}
