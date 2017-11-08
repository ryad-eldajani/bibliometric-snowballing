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


class ProjectsController extends AbstractController
{
    /**
     * URL: /projects
     * Methods: GET
     * @return string rendered template
     */
    public function viewProjectsAction()
    {
        return $this->app->renderTemplate(
            'projects',
            array('dataTable' => true)
        );
    }

    /**
     * URL: /projects/new
     * Methods: POST
     * @return string rendered template
     */
    public function newProjectAction()
    {
        echo \json_encode(
            array(
                'project_name' => $this->http->getRequestInfo('post_params/project_name'),
                'project_id' => $this->http->getRequestInfo('post_params/project_id')
            )
        );
        exit();
    }
}
