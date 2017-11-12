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

class LoginController extends AbstractController
{
    /**
     * URL: /login
     * Methods: GET, POST
     * @return RedirectResponse|Response instance
     */
    public function loginAction()
    {
        // If user is logged in, redirect to projects
        if ($this->userManager->isLoggedIn()) {
            return new RedirectResponse('/projects');
        }

        $message = null;

        // If HTTP method is POST, try to login.
        if ($this->http->getRequestInfo('request_method') == 'post') {
            // If login succeeds, redirect to /, otherwise show login again
            // with message.
            if ($this->userManager->login(
                    $this->http->getPostParam('username'),
                    $this->http->getPostParam('password')
                )
            ) {
                return new RedirectResponse('/');
            } else {
                $message = array(
                    'message' => 'Username and password did not match, please retry.',
                    'messageType' => 'warning'
                );
            }
        }

        return new Response(
            $this->app->renderTemplate('login', $message)
        );
    }

    /**
     * URL: /logout
     * Methods: GET
     * @return RedirectResponse instance
     */
    public function logoutAction()
    {
        $this->userManager->logout();
        return new RedirectResponse('/');
    }
}
