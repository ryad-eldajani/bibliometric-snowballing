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
use BS\Helper\ValidatorHelper;
use BS\Model\Http\Response;
use BS\Model\Http\JsonResponse;

class ProjectController extends AbstractController
{
    /**
     * URL: /projects
     * Methods: GET
     * @return Response instance
     */
    public function viewProjectsAction()
    {
        $this->redirectIfNotLoggedIn();

        return new Response(
            $this->app->renderTemplate(
                'projects',
                array(
                    'dataTable' => true,
                    'projects' => Project::read()
                )
            )
        );
    }

    /**
     * URL: /projects/view/{projectId}
     * Methods: GET
     * @param array $params variable URL params
     * @return Response instance
     */
    public function viewProjectAction(array $params = array())
    {
        $this->redirectIfNotLoggedIn();
        $project = Project::read($params['projectId']);
        $project->getWorkList();

        return new Response(
            $this->app->renderTemplate(
                'project',
                array(
                    'project' => $project,
                    'projects' => Project::read()
                )
            )
        );
    }

    /**
     * URL: /projects/new
     * Methods: POST
     * @return Response instance
     */
    public function newProjectAction()
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
            'project_name' => array(
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 250
            )
        );
        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // Ajax request is validated, create project entity in database.
        $project = new Project(
            null,
            $this->http->getPostParam('project_name'),
            time() * 1000,
            $this->userManager->getUserParam('uid')
        );
        $project->create();

        return new JsonResponse($project);
    }

    /**
     * URL: /projects/delete
     * Methods: POST
     * @return Response instance
     */
    public function deleteProjectAction()
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
            )
        );
        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        $project = Project::read($this->http->getPostParam('project_id'));

        // Check, if current user is project owner.
        $userId = $this->userManager->getUserParam('uid');
        if ($project === null || $project->getUserId() != $userId) {
            return new JsonResponse(
                array('error' => 'Deletion denied.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // User is project owner, delete project.
        $project->delete();

        return new JsonResponse(
            array('project_id' => $this->http->getPostParam('project_id'))
        );
    }

    /**
     * URL: /projects/rename
     * Methods: POST
     * @return Response instance
     */
    public function renameProjectAction()
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
            'project_name' => array(
                'required' => true
            )
        );
        if (!ValidatorHelper::instance()->validate($validationInfo)) {
            return new JsonResponse(
                array('error' => 'Form validation failed.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        $project = Project::read($this->http->getPostParam('project_id'));

        // Check, if current user is project owner.
        $userId = $this->userManager->getUserParam('uid');
        if ($project === null || $project->getUserId() != $userId) {
            return new JsonResponse(
                array('error' => 'Renaming denied.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // User is project owner, update project.
        $project->setName($this->http->getPostParam('project_name'));
        $project->update();

        return new JsonResponse($project);
    }
}
