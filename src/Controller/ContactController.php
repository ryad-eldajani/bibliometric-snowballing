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
use BS\Model\Http\Response;
use BS\Helper\MailHelper;
use PHPMailer\PHPMailer\Exception;

class ContactController extends AbstractController
{
    /**
     * URL: /contact
     * Methods: GET, POST
     * @return Response instance
     */
    public function contactAction()
    {
        $message = null;

        // If HTTP method is POST, try to send an email.
        if ($this->http->getRequestInfo('request_method') == 'post') {

            // Validate posted values.
            $validationInfo = array(
                'name' => array(
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
                ),
                'message' => array(
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 1000
                )
            );
            if (!ValidatorHelper::instance()->validate($validationInfo)) {
                $message = array(
                    'message' => 'Please provide all required information.',
                    'messageType' => 'warning'
                );
            } else {
                try {
                    MailHelper::instance()->sendToAdmin(
                        'Contact Us',
                        'Name: ' . $this->http->getPostParam('name')
                        . PHP_EOL . 'Email: ' . $this->http->getPostParam('email')
                        . PHP_EOL . 'Message: ' . $this->http->getPostParam('message')
                    );
                    $message = array(
                        'message' => 'Your message has been sent, thank you for your feedback!',
                        'messageType' => 'success'
                    );
                } catch (Exception $exception) {
                    $message = array(
                        'message' => 'Unfortunately, due to technical issues, the message could not be sent. Please try again later.',
                        'messageType' => 'danger'
                    );
                }
            }
        }

        return new Response(
            $this->app->renderTemplate('contact', $message)
        );
    }
}
