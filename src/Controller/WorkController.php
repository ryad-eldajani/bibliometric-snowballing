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
     * URL: /works/new
     * Methods: POST
     * @return Response instance
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
        );
        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Ajax request is validated, create project entity in database.
        $work = new Work(
            null,
            $this->http->getPostParam('work_title'),
            $this->http->getPostParam('work_subtitle'),
            $this->http->getPostParam('work_year'),
            $this->http->getPostParam('work_doi')
        );
        $project = Project::read($this->http->getPostParam('project_id'));
        $work->create();
        $project->addWorkId($work->getId());
        $project->update();

        return new JsonResponse($work);
    }
}
