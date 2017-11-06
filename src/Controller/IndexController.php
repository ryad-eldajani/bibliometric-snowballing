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

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $this->redirectIfNotLoggedIn();
        return App::instance()->renderTemplate('projects');
    }

    public function notFoundAction()
    {
        return App::instance()->renderTemplate('404');
    }
}
