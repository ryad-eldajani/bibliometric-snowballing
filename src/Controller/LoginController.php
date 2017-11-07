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
        if (Http::instance()->getRequestInfo('request_method') == 'post') {
            if (UserManager::instance()->login(
                    Http::instance()->getRequestInfo('post_params/username'),
                    Http::instance()->getRequestInfo('post_params/password')
                )
            ) {
                Http::instance()->redirect('/');
            } else {
                $message = array(
                    'message' => 'Username and password did not match, please retry.',
                    'messageType' => 'warning'
                );
            }
        }

        return App::instance()->renderTemplate('login', $message);
    }

    public function logoutAction()
    {
        UserManager::instance()->logout();
        Http::instance()->redirect('/');
    }
}
