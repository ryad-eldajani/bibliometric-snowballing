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


use BS\Model\App;
use BS\Model\Http\Http;
use BS\Model\User\UserManager;

class LoginController extends AbstractController
{
    public function loginAction()
    {
        $message = null;
        if ($this->http->getRequestInfo('request_method') == 'post') {
            if ($this->userManager->login(
                    $this->http->getRequestInfo('post_params/username'),
                    $this->http->getRequestInfo('post_params/password')
                )
            ) {
                $this->http->redirect('/');
            } else {
                $message = array(
                    'message' => 'Username and password did not match, please retry.',
                    'messageType' => 'warning'
                );
            }
        }

        return $this->app->renderTemplate('login', $message);
    }

    public function logoutAction()
    {
        $this->userManager->logout();
        $this->http->redirect('/');
    }
}
