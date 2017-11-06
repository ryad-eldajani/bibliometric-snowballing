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

namespace BS\User;

use BS\Model\Db\Database;

class UserManager
{
    /**
     * @var UserManager|null instance
     */
    protected static $instance = null;

    /**
     * @var array User information cache.
     */
    protected $userInformation = array();

    /**
     * UserManager constructor.
     */
    private function __construct()
    {
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
     * Returns user information for a specific username or null.
     *
     * @param string $username username
     * @return array|null User information from database or null.
     */
    public function getUserInformation($username)
    {
        if (isset($this->userInformation[$username])) {
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
        return $result[0];
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
