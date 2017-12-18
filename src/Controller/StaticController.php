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

class StaticController extends AbstractController
{
    /**
     * URL: /contact
     * Methods: GET
     * @return Response instance
     */
    public function viewContactAction()
    {
        return new Response(
            $this->app->renderTemplate('contact')
        );
    }

    /**
     * URL: /about
     * Methods: GET
     * @return Response instance
     */
    public function viewAboutAction()
    {
        return new Response(
            $this->app->renderTemplate('about')
        );
    }
}
