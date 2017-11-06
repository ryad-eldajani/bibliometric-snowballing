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

namespace BS\Model;

use BS\Model\Exceptions\AppException;
use BS\Model\Typo3\Typo3System;
use League\Plates\Engine;

class App
{
    /**
     * @var App $instance Singleton instance
     */
    protected static $instance = null;

    /**
     * @var array $config Application configuration
     */
    protected $config = null;

    /**
     * @var Typo3System $typo3System Typo3 hook
     */
    protected $typo3System = null;

    /**
     * @var Engine $templateEngine Plates template engine
     */
    protected $templateEngine = null;

    /**
     * @var array|bool $urlComponents parsed URL components
     */
    protected $urlComponents = null;

    /**
     * @var array $urlInfo URL information about controllers etc.
     */
    protected $urlInfo = null;

    /**
     * App constructor.
     */
    private function __construct()
    {
        set_exception_handler(array($this, 'handleException'));
        $this->templateEngine = new Engine('templates');
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
     * Global exception handler.
     * @param \Throwable $exception throwable instance
     */
    public function handleException($exception)
    {
        echo $this->renderTemplate('exception', array('exception' => $exception));
        exit(1);
    }

    /**
     * Handles the request, calls controllers etc.
     */
    public function handleRequest()
    {
        $availableUrls = $this->getConfig('urls');
        $requestBasePath = 'http' . ($_SERVER['SERVER_PORT'] == 443 ? 's' : '')
            . '://' . $_SERVER['HTTP_HOST'];
        $requestFullPath =  $requestBasePath . $_SERVER['REQUEST_URI'];
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $this->urlComponents = parse_url($requestFullPath);
        $this->urlComponents['base_path'] = $requestBasePath;
        $requestPath = $this->urlComponents['path'];

        // Redirect to 404 if URL path is not registered.
        if (!isset($availableUrls[$requestPath])) {
            $this->redirect('/404');
        }

        $urlInfo = $availableUrls[$requestPath];
        $this->urlInfo = $urlInfo;

        // Redirect to 404, if the request method is invalid.
        if (!in_array($requestMethod, $urlInfo['methods'])) {
            $this->redirect('/404');
        }

        // Instantiate controller and call action.
        $controllerInfo = explode('/', $urlInfo['controller']);
        $controllerName = 'BS\\Controller\\' . $controllerInfo[0] . 'Controller';
        $actionName = $controllerInfo[1] . 'Action';
        echo (new $controllerName)->$actionName();
    }

    /**
     * Sends a HTTP redirect.
     *
     * @param string $path URL path to redirect to
     */
    public function redirect($path)
    {
        header("Location: " . $this->urlComponents['base_path'] . $path);
        exit();
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

    /**
     * Renders a template using the template Engine.
     *
     * @param string $templateName template name
     * @param array $templateParameters parameters for the template
     * @return string rendered template
     */
    public function renderTemplate($templateName, array $templateParameters = array())
    {
        return $this->templateEngine->render($templateName, $templateParameters);
    }
}
