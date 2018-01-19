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

class UserController extends AbstractController
{
    /**
     * URL: /profile
     * Methods: GET, POST
     * @return Response instance
     */
    public function profileAction()
    {
        $this->redirectIfNotLoggedIn();

        $message = null;

        // If this is a POST request, alter user parameters.
        if ($this->isPostRequest()) {
            $validationInfo = array(
                'current_password' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 6,
                    'max' => 30
                ),
                'new_password' => array(
                    'required' => false,
                    'type' => 'string',
                    'min' => 0,
                    'max' => 30
                ),
                'new_password_confirm' => array(
                    'required' => false,
                    'type' => 'string',
                    'min' => 0,
                    'max' => 30
                ),
                'email' => array(
                    'required' => false,
                    'type' => 'email',
                    'min' => 1,
                    'max' => 250
                ),
                'country' => array(
                    'required' => false,
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
            } else if (!$this->userManager->checkCredentials(
                    $this->userManager->getUserParam('username'),
                    $this->http->getPostParam('current_password')
                )
            ) {
                // Current password is invalid
                $message = array(
                    'message' => 'Your current password is invalid. Please try again, or <a href="/password_reset">reset your password</a>.',
                    'messageType' => 'danger'
                );
            } else {
                $profileUpdate = $this->userManager->updateProfile();
                if ($profileUpdate === true) {
                    // Profile update succeeds
                    $message = array(
                        'message' => 'Your profile has been updated.',
                        'messageType' => 'success'
                    );
                } else {
                    $message = array(
                        'message' => $profileUpdate,
                        'messageType' => 'danger'
                    );
                }
            }
        }

        return new Response(
            $this->app->renderTemplate('profile', $message)
        );
    }

    /**
     * URL: /register
     * Methods: GET, POST
     * @return RedirectResponse|Response instance
     */
    public function registerAction()
    {
        // If user is logged in, redirect to projects
        if ($this->isUserLoggedIn()) {
            return new RedirectResponse('/projects');
        }

        $message = null;

        // If HTTP method is POST, try to register.
        if ($this->isPostRequest()) {
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
                    return (new LoginController())->loginAction();
                } else {
                    $message = array(
                        'message' => $register,
                        'messageType' => 'danger'
                    );
                }
            }
        }

        return new Response(
            $this->app->renderTemplate('register', $message)
        );
    }

    /**
     * URL: /password_reset
     * Methods: GET, POST
     * @return Response instance
     */
    public function passwordResetAction()
    {
        $message = null;

        // If HTTP method is POST, try to register.
        if ($this->isPostRequest()) {
            $validationInfo = array(
                'username' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 250
                ),
                'email' => array(
                    'required' => true,
                    'type' => 'email',
                    'min' => 1,
                    'max' => 250
                )
            );
            if (!ValidatorHelper::instance()->validate($validationInfo)) {
                $message = array(
                    'message' => 'Please provide all required information.',
                    'messageType' => 'warning'
                );
            } else {
                $this->userManager->passwordReset(
                    $this->http->getPostParam('username'),
                    $this->http->getPostParam('email')
                );

                $message = array(
                    'message' => 'A new password has been sent to your email address. Please follow the instructions in the email.',
                    'messageType' => 'success'
                );
            }
        }

        return new Response(
            $this->app->renderTemplate('password_reset', $message)
        );
    }
}
