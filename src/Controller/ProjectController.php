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


use BS\Model\Http\Response;

class ProjectController extends AbstractController
{
    /**
     * URL: /projects
     * Methods: GET
     * @return Response instance
     */
    public function viewProjectsAction()
    {
        return new Response(
            $this->app->renderTemplate(
                'projects',
                array('dataTable' => true)
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
        return new Response(
            \json_encode(
                array(
                    'project_name' => $this->http->getRequestInfo('post_params/project_name'),
                    'project_id' => $this->http->getRequestInfo('post_params/project_id')
                )
            ),
            Response::HTTP_STATUS_OK,
            Response::CONTENT_TYPE_JSON
        );
    }
}
