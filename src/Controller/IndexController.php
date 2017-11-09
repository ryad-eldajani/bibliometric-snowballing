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


use BS\Model\Http\RedirectResponse;
use BS\Model\Http\Response;

class IndexController extends AbstractController
{
    /**
     * URL: /
     * Methods: GET
     * @return RedirectResponse redirect to projects or login
     */
    public function indexAction()
    {
        $this->redirectIfNotLoggedIn();
        return new RedirectResponse('/projects');
    }

    /**
     * URL: /404
     * Methods: GET
     * @return Response instance
     */
    public function notFoundAction()
    {
        return new Response(
            $this->app->renderTemplate('404'),
            Response::HTTP_STATUS_NOT_FOUND
        );
    }
}
