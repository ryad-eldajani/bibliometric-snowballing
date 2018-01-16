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
            $validationInfo = array(
                'username' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 250
                ),
                'password' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 250
                ),
            );
            if (!ValidatorHelper::instance()->validate($validationInfo)) {
                $message = array(
                    'message' => 'Please provide all required information.',
                    'messageType' => 'warning'
                );
            } else {
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

    /**
     * URL: /register
     * Methods: GET, POST
     * @return RedirectResponse|Response instance
     */
    public function registerAction()
    {
        // If user is logged in, redirect to projects
        if ($this->userManager->isLoggedIn()) {
            return new RedirectResponse('/projects');
        }

        $message = null;

        // If HTTP method is POST, try to register.
        if ($this->http->getRequestInfo('request_method') == 'post') {
            $validationInfo = array(
                'username' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 250
                ),
                'password' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 6,
                    'max' => 30
                ),
                'password_confirm' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 6,
                    'max' => 30
                ),
                'email' => array(
                    'required' => true,
                    'type' => 'email',
                    'min' => 1,
                    'max' => 250
                ),
                'country' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 40
                ),
                'university' => array(
                    'required' => false,
                    'type' => 'string',
                    'min' => 0,
                    'max' => 80
                ),
            );
            if (!ValidatorHelper::instance()->validate($validationInfo)) {
                $message = array(
                    'message' => 'Please provide all required information.',
                    'messageType' => 'warning'
                );
            } else if ($this->http->getPostParam('password') !== $this->http->getPostParam('password_confirm')) {
                $message = array(
                    'message' => 'Passwords do not match.',
                    'messageType' => 'warning'
                );
            } else {
                $register = $this->userManager->register(
                    $this->http->getPostParam('username'),
                    $this->http->getPostParam('password'),
                    $this->http->getPostParam('email'),
                    $this->http->getPostParam('country'),
                    $this->http->getPostParam('university')
                );

                if ($register === true) {
                    // Register succeeds, return login
                    return $this->loginAction();
                } else {
                    $message = array(
                        'message' => $register,
                        'messageType' => 'error'
                    );
                }
            }
        }

        return new Response(
            $this->app->renderTemplate('register', $message)
        );
    }
}
