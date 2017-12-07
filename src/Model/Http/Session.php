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

namespace BS\Model\Http;

/**
 * Class Session.
 * Based on: http://eddmann.com/posts/securing-sessions-in-php/
 * @package BS\Model\Http
 */
class Session extends \SessionHandler
{
    protected $name = null;
    protected $cookie = null;

    /**
     * Session constructor.
     *
     * @param string $name
     * @param array $cookie
     */
    public function __construct($name = 'BS_SESSION', $cookie = array())
    {
        $this->name = $name;
        $this->cookie = $cookie;

        $this->cookie += [
            'lifetime' => 0,
            'path'     => ini_get('session.cookie_path'),
            'domain'   => ini_get('session.cookie_domain'),
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true
        ];

        $this->setup();
    }

    /**
     * Sets up the session.
     */
    protected function setup()
    {
        session_name($this->name);

        session_set_cookie_params(
            $this->cookie['lifetime'],
            $this->cookie['path'],
            $this->cookie['domain'],
            $this->cookie['secure'],
            $this->cookie['httponly']
        );
    }

    /**
     * Starts the session.
     *
     * @return bool True, if started.
     */
    public function start()
    {
        if (session_id() === '') {
            if (session_start()) {
                return (mt_rand(0, 4) === 0) ? $this->refresh() : true; // 1/5
            }
        }

        return false;
    }

    /**
     * Forgets the session.
     *
     * @return bool True, if forgotten.
     */
    public function forget()
    {
        if (session_id() === '') {
            return false;
        }

        $_SESSION = [];

        setcookie(
            $this->name, '', time() - 42000,
            $this->cookie['path'], $this->cookie['domain'],
            $this->cookie['secure'], $this->cookie['httponly']
        );

        return session_destroy();
    }

    /**
     * Refreshes the session.
     *
     * @return bool True, if refreshed.
     */
    public function refresh()
    {
        return session_regenerate_id();
    }

    /**
     * Returns true, if the session is expired.
     *
     * @param int $ttl Session TTL
     * @return bool True, if expired
     */
    public function isExpired($ttl = 30)
    {
        $activity = isset($_SESSION['_last_activity'])
            ? $_SESSION['_last_activity']
            : false;

        if ($activity !== false && time() - $activity > $ttl * 60) {
            return true;
        }

        $_SESSION['_last_activity'] = time();

        return false;
    }

    /**
     * Returns true, if the fingerprint of the HTTP user agent is valid.
     *
     * @return bool True, if fingerprint is valid.
     */
    public function isFingerprint()
    {
        $hash = md5(
            $_SERVER['HTTP_USER_AGENT'] .
            (ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0'))
        );

        if (isset($_SESSION['_fingerprint'])) {
            return $_SESSION['_fingerprint'] === $hash;
        }

        $_SESSION['_fingerprint'] = $hash;

        return true;
    }

    /**
     * Returns true, if the session is valid.
     *
     * @param int $ttl Session TTL
     * @return bool True, if session is valid
     */
    public function isValid($ttl = 30)
    {
        return ! $this->isExpired($ttl) && $this->isFingerprint();
    }

    /**
     * Returns a session value by key.
     *
     * @param string $key Key for the value
     * @return null|string Value for the key
     */
    public function getValue($key)
    {
        $parsed = explode('.', $key);

        $result = $_SESSION;

        while ($parsed) {
            $next = array_shift($parsed);

            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return null;
            }
        }

        return $result;
    }

    /**
     * Returns true, if a value is given for a key.
     *
     * @param string $key Key for the value
     * @return bool True, if a value exists for a key
     */
    public function hasValue($key)
    {
        return $this->getValue($key) !== null;
    }

    /**
     * Sets a value into the session by a key.
     *
     * @param string $key Key for the value
     * @param string $value Value for the key
     */
    public function setValue($key, $value)
    {
        $parsed = explode('.', $key);

        $session =& $_SESSION;

        while (count($parsed) > 1) {
            $next = array_shift($parsed);

            if ( ! isset($session[$next]) || ! is_array($session[$next])) {
                $session[$next] = [];
            }

            $session =& $session[$next];
        }

        $session[array_shift($parsed)] = $value;
    }
}
