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

use BS\Helper\MailHelper;
use BS\Model\App;
use BS\Model\Db\Database;
use BS\Model\Http\Http;
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
     * Returns a TYPO3 SaltingInstance.
     *
     * @param null|string $password Password for SaltingInstance
     * @return null|\TYPO3\CMS\Saltedpasswords\Salt\SaltInterface SaltingInstance or null
     */
    protected function getSaltingInstance($password = null)
    {
        if (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled('FE')) {
            $saltingInstance = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance($password);
            if (is_object($saltingInstance)) {
                return $saltingInstance;
            }
        }

        return null;
    }

    /**
     * Returns a salted password by TYPO3 API.
     *
     * @param string $password Password to salt
     * @return null|string Salted password or null
     */
    protected function getSaltedPassword($password)
    {
        $saltingInstance = $this->getSaltingInstance();
        if ($saltingInstance === null) {
            return null;
        }

        return $saltingInstance->getHashedPassword($password);
    }

    /**
     * Registers a new user.
     *
     * @param string $username Username for new user
     * @param string $password Password for new user
     * @param string $email Email address of user
     * @param string $country Origin country of user
     * @param string $university University of user
     * @return bool|string True, if new user is registered, otherwise string with error message.
     */
    public function register($username, $password, $email, $country, $university = '')
    {
        if ($this->getUserInformation($username) !== null) {
            Http::instance()->alterPostParam('username', '');
            return 'Username already given.';
        }

        $saltedPassword = $this->getSaltedPassword($password);
        if ($saltedPassword === null) {
            return 'Due to technical issues, registration is temporary unavailable. '
                    . 'Please try again later or <a href="/contact">contact us</a>.';
        }

        $userInformation = array(
            'username' => $username,
            'password' => $saltedPassword,
            'email' => $email,
            'country' => $country,
            'company' => $university
        );

        if (!$this->setUserInformation(null, $userInformation)) {
            return 'Due to technical issues, registration is temporary unavailable. '
                . 'Please try again later or <a href="/contact">contact us</a>.';
        }

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
     * Returns user information by parameter.
     *
     * @param string $key Parameter key
     * @param null|string $username Username or null for currently logged in user
     * @return mixed|null Parameter value or null
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
     * Sets user information.
     *
     * @param null|int $userId User ID or null for new users
     * @param array $userInformation User information
     * @return bool True, if user information is inserted/updated
     */
    protected function setUserInformation($userId = null, $userInformation = array())
    {
        // Set SQL parameters based on values from $userInformation.
        $sqlParams = array_values($userInformation);

        if ($userId === null) {
            // Add default values for TYPO3 relation 'fe_users'.
            $sqlParams[] = 2;            // pid
            $sqlParams[] = time();       // tstamp
            $sqlParams[] = 1;            // usergroup
            $sqlParams[] = 0;            // disable
            $sqlParams[] = 0;            // starttime
            $sqlParams[] = 0;            // endtime
            $sqlParams[] = 0;            // deleted
            $sqlParams[] = 1;            // cruser_id
            $sqlParams[] = time();       // crdate
            $sqlParams[] = 0;            // lastonline
            $sqlParams[] = 0;            // is_online

            return is_numeric(Database::instance()->insert(
                'INSERT INTO fe_users (username, password, email, country, company, pid, tstamp, usergroup, disable, '
                . 'starttime, endtime, deleted, cruser_id, crdate, lastlogin, is_online) VALUES '
                . '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                $sqlParams,
                Database::CONNECTION_TYPO3
            ));
        } else {
            // Add user ID to SQL parameters for WHERE clause.
            $sqlParams[] = $userId;
            Database::instance()->updateOrDelete(
                'UPDATE fe_users SET ' . join(' = ?, ', array_keys($userInformation)) . ' = ? WHERE uid = ?',
                $sqlParams,
                Database::CONNECTION_TYPO3
            );

            return true;
        }
    }

    /**
     * Generates a random password.
     * Based on: https://stackoverflow.com/a/6101969
     *
     * @param int $length Password length
     * @return string Random password
     */
    protected function getRandomPassword($length = 8)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $passwordParts = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $passwordParts[] = $alphabet[$n];
        }

        return implode($passwordParts);
    }

    /**
     * Stores a new random password and sends an email to the user.
     *
     * @param string $username
     * @param string $email
     */
    public function passwordReset($username, $email)
    {
        // If username and email does not match, abort.
        if ($this->getUserParam('email', $username) !== $email) {
            return;
        }

        // Create random password and store for user.
        $randomPassword = $this->getRandomPassword();
        $saltedRandomPassword = $this->getSaltedPassword($randomPassword);
        if ($saltedRandomPassword === null) {
            return;
        }

        $this->setUserInformation(
            $this->getUserParam('uid', $username),
            array(
                'password' => $saltedRandomPassword
            )
        );

        try {
            MailHelper::instance()->sendToAddress(
                'Password Reset',
                'Dear ' . $username . ',' . PHP_EOL
                . 'You have requested a new password.' . PHP_EOL . PHP_EOL
                . 'Your new password is: ' . $randomPassword . PHP_EOL . PHP_EOL
                . 'Please update your password as soon as possible.' . PHP_EOL . PHP_EOL
                . 'Best Regards,' . PHP_EOL
                . 'Your Bibliometric Snowballing Team',
                $email
            );
        } catch (\Exception $exception) {}
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

        $saltingInstance = $this->getSaltingInstance($userInfo['password']);
        if ($saltingInstance === null) {
            return false;
        }

        return $saltingInstance->checkPassword($password, $userInfo['password']);
    }
}
