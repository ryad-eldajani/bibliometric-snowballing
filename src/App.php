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

namespace BS;

use BS\Exceptions\AppException;
use BS\Typo3\Typo3System;

class App
{
    protected static $instance = null;
    protected $config = null;
    protected $typo3System = null;

    /**
     * App constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton.
     * @return App instance
     */
    public static function instance()
    {
        if (!isset(App::$instance)) {
            $app = new App();
            App::$instance = $app;
            $app->typo3System = new Typo3System($app);
        }

        return App::$instance;
    }

    /**
     * Loads the configuration.
     * @throws AppException
     */
    protected function loadConfiguration()
    {
        if (!$this->config) {
            if (!file_exists('conf/config.json')) {
                throw new AppException('Missing config.json file in conf directory');
            }

            $json = file_get_contents('conf/config.json');
            $this->config = \json_decode($json, true);
        }
    }

    /**
     * Returns a configuration setting by path.
     * E.g. $path = 'db/hostname' returns the database hostname.
     *
     * @param string $path configuration path
     * @return null|string|integer Configuration value
     */
    public function getConfig($path)
    {
        if (!isset($this->config)) {
            $this->loadConfiguration();
        }

        $explodedPath = explode('/', $path);
        $value = $this->config;

        foreach ($explodedPath as $key) {
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Returns the Typo3 system instance.
     * @return Typo3System instance
     */
    public function getTypo3System()
    {
        return $this->typo3System;
    }
}
