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


use BS\Helper\ValidatorHelper;
use BS\Model\Http\Response;
use Model\Http\JsonResponse;

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

        $projects = $this->db->select(
            'SELECT p.id_project, p.project_name, p.created_at,
            (SELECT COUNT(*) FROM work_project wp WHERE wp.id_project = p.id_project) AS objects
            FROM project p
            WHERE p.id_user = ?',
            array($this->userManager->getUserParam('uid'))
        );

        return new Response(
            $this->app->renderTemplate(
                'projects',
                array(
                    'dataTable' => true,
                    'projects' => count($projects) > 0 ? $projects : null
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

        // Ajax request is validated, insert into database.
        $projectId = $this->db->insert(
            'INSERT INTO project (`id_user`, `project_name`) VALUES (?, ?)',
            array(
                $this->userManager->getUserParam('uid'),
                $this->http->getPostParam('project_name')
            )
        );

        return new JsonResponse(
            $this->db->select(
                'SELECT * FROM project WHERE id_project = ?',
                array($projectId)
            )
        );
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

        // Check, if current user is project owner.
        $userId = $this->userManager->getUserParam('uid');
        $isOwner = $this->db->select(
            'SELECT id_project FROM project WHERE id_project = ? AND id_user = ?',
            array(
                $this->http->getPostParam('project_id'),
                $userId
            )
        );
        if (count($isOwner) !== 1) {
            return new JsonResponse(
                array('error' => 'Deletion denied.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // User is project owner, delete project.
        $this->db->updateOrDelete(
            'DELETE FROM project WHERE id_project = ?',
            array($this->http->getPostParam('project_id'))
        );

        return new JsonResponse(
            array('id_project' => $this->http->getPostParam('project_id'))
        );
    }
}
