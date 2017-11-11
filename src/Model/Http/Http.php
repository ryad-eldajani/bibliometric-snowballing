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

use BS\Controller\IController;
use BS\Helper\ArrayHelper;
use BS\Model\App;

class Http
{
    /**
     * @var Http $instance Singleton instance
     */
    protected static $instance = null;

    /**
     * @var array $requestInfo information about the request.
     */
    protected $requestInfo = null;

    /**
     * @var array $controllerInfo information about the used controller etc.
     */
    protected $controllerInfo = null;

    /**
     * Http constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton.
     * @return Http instance
     */
    public static function instance()
    {
        if (!isset(Http::$instance)) {
            Http::$instance = new Http();
        }

        return Http::$instance;
    }

    /**
     * Sets up the request information.
     */
    protected function setupRequestInfo()
    {
        $isHttps = $_SERVER['SERVER_PORT'] == 443;
        $requestBasePath = ($isHttps ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $requestFullPath =  $requestBasePath . $_SERVER['REQUEST_URI'];
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $this->requestInfo = parse_url($requestFullPath);
        $this->requestInfo['base_path'] = $requestBasePath;
        $this->requestInfo['is_https'] = $isHttps;
        $this->requestInfo['request_method'] = $requestMethod;
        $this->requestInfo['get_params'] = $_GET;

        if ($this->requestInfo['request_method'] == 'post') {
            $this->requestInfo['post_params'] = $_POST;
        }
    }

    /**
     * Handles the request, calls controllers etc.
     */
    public function handleRequest()
    {
        $this->setupRequestInfo();
        $availableUrls = App::instance()->getConfig('urls');

        // Redirect to 404 if URL path is not registered.
        if (!isset($availableUrls[$this->requestInfo['path']])) {
            (new RedirectResponse('/404'))->send();
        }

        $this->controllerInfo = $availableUrls[$this->requestInfo['path']];

        // Redirect to 404, if the request method is invalid.
        if (!in_array($this->requestInfo['request_method'], $this->controllerInfo['methods'])) {
            (new RedirectResponse('/404'))->send();
        }

        // Instantiate controller and call action.
        $controllerClassAction = explode('/', $this->controllerInfo['controller']);
        $this->controllerInfo['controller_name'] = 'BS\\Controller\\'
            . $controllerClassAction[0] . 'Controller';
        $this->controllerInfo['action_name'] = $controllerClassAction[1] . 'Action';

        /**
         * @var $controller IController
         */
        $controller = new $this->controllerInfo['controller_name'];

        /**
         * @var $response Response
         */
        $response = $controller->{$this->controllerInfo['action_name']}();
        $response->send();
    }

    /**
     * Sends a HTTP redirect.
     *
     * @param string $path URL path to redirect to
     * @param null|array $parameters optional parameters
     */
    public function redirect($path, $parameters = null)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $parameterKey => $parameterValue) {
                header(
                    'BS-' . (string)$parameterKey . ': '
                    . (string)$parameterValue
                );
            }
        }

        header('Location: ' . $this->requestInfo['base_path'] . $path);
        exit();
    }

    /**
     * Getter for the request information.
     *
     * @param string|null $path optional path for the specific request information
     * @return mixed
     */
    public function getRequestInfo($path = null)
    {
        return $path === null
            ? $this->requestInfo
            : ArrayHelper::instance()->getValueByPath($this->requestInfo, $path);
    }

    /**
     * Returns a POST variable, if available
     *
     * @param string $key POST variable name to return
     * @return null|string value of POST variable or null if not available
     */
    public function getPostParam($key) {
        if (
            !isset($this->requestInfo['post_params'])
            || !isset($this->requestInfo['post_params'][$key])
        ) {
            return null;
        }

        return $this->requestInfo['post_params'][$key];
    }

    /**
     * Alters a POST variable, if available
     *
     * @param string $key POST variable name to alter
     * @param string $value POST variable value to set
     * @return null|string altered POST variable or null if not available
     */
    public function alterPostParam($key, $value) {
        if (
            !isset($this->requestInfo['post_params'])
            || !isset($this->requestInfo['post_params'][$key])
        ) {
            return null;
        }

        $this->requestInfo['post_params'][$key] = $value;
        return $this->requestInfo['post_params'][$key];
    }

    /**
     * Getter for the controller information by key.
     *
     * @param string|null $path optional path for the specific controller information
     * @return mixed controller information
     */
    public function getControllerInfo($path)
    {
        return $path === null
            ? $this->controllerInfo
            : ArrayHelper::instance()->getValueByPath($this->controllerInfo, $path);
    }
}
