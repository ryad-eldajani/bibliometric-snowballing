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
use BS\Model\Db\Database;
use BS\Model\Http\Http;
use BS\Model\Http\JsonResponse;
use BS\Model\Http\RedirectResponse;
use BS\Model\Http\Response;
use BS\Model\User\UserManager;

abstract class AbstractController implements IController
{
    /**
     * @var App $app App instance
     */
    protected $app = null;

    /**
     * @var Http $http Http instance
     */
    protected $http = null;

    /**
     * @var UserManager $userManager UserManager instance
     */
    protected $userManager = null;

    /**
     * @var Database $db Database instance
     */
    protected $db = null;

    /**
     * AbstractController constructor.
     */
    public function __construct()
    {
        $this->app = App::instance();
        $this->http = Http::instance();
        $this->userManager = UserManager::instance();
        $this->db = Database::instance();
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

    /**
     * Checks, if the user is logged in. If the user is not logged in
     * a JsonResponse with error will be sent.
     */
    protected function errorJsonResponseIfNotLoggedIn()
    {
        if (!$this->userManager->isLoggedIn()) {
            (new JsonResponse(
                array('error' => 'Not logged in.'),
                Response::HTTP_STATUS_BAD_REQUEST)
            )->send();
        }
    }
}
