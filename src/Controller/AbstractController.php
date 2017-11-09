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
use BS\Model\Http\RedirectResponse;
use BS\Model\User\UserManager;

abstract class AbstractController implements IController
{
    /**
     * @var App app instance
     */
    protected $app = null;

    /**
     * @var Http instance
     */
    protected $http = null;

    /**
     * @var UserManager user manager instance
     */
    protected $userManager = null;

    /**
     * AbstractController constructor.
     */
    public function __construct()
    {
        $this->app = App::instance();
        $this->http = Http::instance();
        $this->userManager = UserManager::instance();
    }

    /**
     * Checks, if the user is logged in. If the user is not logged in
     * he will be redirected to the login page.
     */
    protected function redirectIfNotLoggedIn()
    {
        if (!$this->userManager->isLoggedIn()) {
            (new RedirectResponse('/login'))->send();
        }
    }
}
