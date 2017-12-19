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
            $message = array(
                'message' => 'Contact us is not implemented yet. Please try again later.',
                'messageType' => 'warning'
            );
        }

        return new Response(
            $this->app->renderTemplate('contact', $message)
        );
    }
}
