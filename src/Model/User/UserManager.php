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

namespace BS\Model\User;

use BS\Model\App;
use BS\Model\Db\Database;
use BS\Model\Http\Session;

class UserManager
{
    /**
     * @var UserManager|null instance
     */
    protected static $instance = null;

    /**
     * @var array|null User information cache.
     */
    protected $userInformation = null;

    /**
     * @var Session|null $session Session
     */
    protected $session = null;

    /**
     * UserManager constructor.
     */
    private function __construct()
    {
        $this->session = new Session();
        $this->session->start();
    }

    /**
     * Returns the singleton.
     * @return UserManager instance
     */
    public static function instance()
    {
        if (!isset(UserManager::$instance)) {
            UserManager::$instance = new UserManager();
        }

        return UserManager::$instance;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->session->hasValue('user.username');
    }

    /**
     * Logs in a user by username.
     *
     * @param string $username Username
     * @param string $password Password
     * @return bool True, if login succeeded
     */
    public function login($username, $password)
    {
        // If session is already available or credential check failed, abort.
        if (!$this->checkCredentials($username, $password)) {
            return false;
        }

        if (!$this->session->isValid(App::instance()->getConfig('session_ttl'))) {
            $this->session->forget();
        }

        $this->session->setValue('user.username', $username);
        return true;
    }

    /**
     * Logs out a user.
     */
    public function logout()
    {
        if (!($this->session instanceof Session)) {
            return;
        }

        $this->session->forget();
        $this->session = null;
    }

    /**
     * @param $key
     * @param null $username
     * @return mixed|null
     */
    public function getUserParam($key, $username = null)
    {
        $userInfo = $this->getUserInformation($username);
        return isset($userInfo[$key]) ? $userInfo[$key] : null;
    }

    /**
     * Returns user information for a specific username or null.
     *
     * @param string $username username
     * @return array|null User information from database or null.
     */
    public function getUserInformation($username = null)
    {
        if ($username === null) {
            if ($this->isLoggedIn()) {
                $username = $this->session->getValue('user.username');
            } else {
                return null;
            }
        }

        if (is_array($this->userInformation[$username])) {
            return $this->userInformation[$username];
        }

        $result = Database::instance()->select(
            'SELECT * FROM fe_users WHERE username = ?',
            array($username),
            Database::CONNECTION_TYPO3
        );

        // If the result count is not exactly 1, we have no valid user data.
        if (count($result) != 1) {
            return null;
        }

        $this->userInformation[$username] = $result[0];
        return $this->userInformation[$username];
    }

    /**
     * Checks the credentials for a username and password.
     *
     * @param string $username Username
     * @param string $password Password
     * @return bool True, if username and password matches
     */
    public function checkCredentials($username, $password)
    {
        $userInfo = $this->getUserInformation($username);
        if ($userInfo === null) {
            return false;
        }

        $success = false;
        if (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled('FE')) {
            $saltingInstance = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance($userInfo['password']);
            if (is_object($saltingInstance)) {
                $success = $saltingInstance->checkPassword($password, $userInfo['password']);
            }
        }

        return $success;
    }
}
