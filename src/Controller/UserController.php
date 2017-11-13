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

class UserController extends AbstractController
{
    /**
     * URL: /profile
     * Methods: GET
     * @return Response instance
     */
    public function viewProfileAction()
    {
        $this->redirectIfNotLoggedIn();

        return new Response(
            $this->app->renderTemplate('profile')
        );
    }
}
