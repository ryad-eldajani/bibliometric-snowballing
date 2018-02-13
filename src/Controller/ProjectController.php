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


use BS\Helper\GraphHelper;
use BS\Model\Entity\Project;
use BS\Helper\ValidatorHelper;
use BS\Model\Entity\Work;
use BS\Model\Http\Response;
use BS\Model\Http\JsonResponse;
use BS\Model\Http\DownloadResponse;

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
                    'dataTable' => true
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

        return new Response(
            $this->app->renderTemplate(
                'project',
                array(
                    'dataTable' => true,
                    'project' => $project
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
        $this->wrongJsonResponseIfNotPost();

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
        $this->wrongJsonResponseIfNotPost();

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
     * URL: /projects/work/remove
     * Methods: POST
     * @return Response instance
     */
    public function removeWorkFromProjectAction()
    {
        $this->errorJsonResponseIfNotLoggedIn();
        $this->wrongJsonResponseIfNotPost();

        // Validate Ajax request.
        $validationInfo = array(
            'project_id' => array(
                'required' => true,
                'type' => 'int'
            ),
            'work_id' => array(
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
        $work = Work::read($this->http->getPostParam('work_id'));

        // Check, if current user is project owner.
        $userId = $this->userManager->getUserParam('uid');
        if ($project === null || $work === null || $project->getUserId() != $userId) {
            return new JsonResponse(
                array('error' => 'Removing denied.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        // User is project owner, remove work.
        if (!$project->removeWork($work)) {
            return new JsonResponse(
                array('error' => 'Removing failed.'),
                Response::HTTP_STATUS_NOT_FOUND
            );
        }

        return new JsonResponse(
            array('work_id' => $this->http->getPostParam('work_id'))
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
        $this->wrongJsonResponseIfNotPost();

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

    /**
     * URL: /projects/request/graph/{projectId}
     * Methods: GET
     * @param array $params variable URL params
     * @return JsonResponse instance
     */
    public function requestGraphAction(array $params = array())
    {
        $project = Project::read($params['projectId']);
        if ($project === null) {
            return new JsonResponse(
                array('error' => 'Project not available.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        return new JsonResponse(GraphHelper::instance()->getGraph($project));
    }

    /**
     * Renders the graph as a $fileType file.
     *
     * @param int $projectId project ID to render
     * @param string $fileType destination file type
     * @return DownloadResponse|JsonResponse response instance
     */
    protected function getGraphAsFile($projectId, $fileType = 'svg')
    {
        $project = Project::read($projectId);
        if ($project === null) {
            return new JsonResponse(
                array('error' => 'Project not available.'),
                Response::HTTP_STATUS_BAD_REQUEST
            );
        }

        if ($fileType == 'svg') {
            $svgXml = GraphHelper::instance()->getGraphAsSvg($project);
            if ($svgXml !== '') {
                return new DownloadResponse($svgXml, $project->getName() . '.svg');
            }
        } else if ($fileType == 'png') {
            $pngContent = GraphHelper::instance()->getGraphAsPng($project);
            if ($pngContent !== '') {
                return new DownloadResponse(
                    $pngContent,
                    $project->getName() . '.png',
                    Response::CONTENT_TYPE_PNG
                );
            }
        } else if ($fileType == 'dot') {
            $dotContent = GraphHelper::instance()->getGraphAsDot($project);
            if ($dotContent !== '') {
                return new DownloadResponse(
                    $dotContent,
                    $project->getName() . '.dot',
                    Response::CONTENT_TYPE_PNG
                );
            }
        }

        return new JsonResponse(
            array('error' => 'Server error while computing SVG.'),
            Response::HTTP_STATUS_SERVER_ERROR
        );
    }

    /**
     * URL: /projects/request/graph/svg/{projectId}
     * Methods: GET
     * @param array $params variable URL params
     * @return DownloadResponse|JsonResponse instance
     */
    public function requestSvgGraphAction(array $params = array())
    {
        return $this->getGraphAsFile($params['projectId'], 'svg');
    }

    /**
     * URL: /projects/request/graph/png/{projectId}
     * Methods: GET
     * @param array $params variable URL params
     * @return DownloadResponse|JsonResponse instance
     */
    public function requestPngGraphAction(array $params = array())
    {
        return $this->getGraphAsFile($params['projectId'], 'png');
    }

    /**
     * URL: /projects/request/graph/dot/{projectId}
     * Methods: GET
     * @param array $params variable URL params
     * @return DownloadResponse|JsonResponse instance
     */
    public function requestDotGraphAction(array $params = array())
    {
        return $this->getGraphAsFile($params['projectId'], 'dot');
    }
}
