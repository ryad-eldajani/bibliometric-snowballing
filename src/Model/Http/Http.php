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

        $this->setControllerInfo($this->requestInfo['path']);
    }

    /**
     * Sets the controller information if available. Otherwise a redirect
     * response to 404 is sent.
     *
     * @param string $requestUrl request URL path
     */
    protected function setControllerInfo($requestUrl)
    {
        $availableUrls = App::instance()->getConfig('urls');
        if (isset($availableUrls[$requestUrl])) {
            // URL is directly available.
            $this->controllerInfo = $availableUrls[$this->requestInfo['path']];
            return;
        } else {
            // Check all variable URLs, if they match with the request URL.
            foreach ($availableUrls as $availableUrl => $controllerInformation) {
                // If the available URL does not contain a curly brace, continue.
                if (strpos($availableUrl, '{') === false) {
                    continue;
                }

                // Check, if static part is part of request URL.
                $variableUrlParts = preg_split('/{+(.*?)}/', $availableUrl);
                if (strpos($requestUrl, $variableUrlParts[0]) !== false) {
                    // Static part is part of request URL, set controller
                    // information and variable parts.
                    if (preg_match_all('/{+(.*?)}/', $availableUrl, $matches)) {
                        $this->controllerInfo = $controllerInformation;

                        // Split variable parts from request URL, combine with
                        // variable names from $matches and set as 'params'.
                        $variableRequestUrlParts = explode(
                            '/',
                            str_replace($variableUrlParts[0], '', $requestUrl)
                        );
                        $this->controllerInfo['params'] = array_combine(
                            $matches[1],
                            $variableRequestUrlParts
                        );
                    }

                    return;
                }
            }
        }

        // Controller is not available, return 404 redirect response.
        (new RedirectResponse('/404'))->send();
    }

    /**
     * Handles the request, calls controllers etc.
     */
    public function handleRequest()
    {
        $this->setupRequestInfo();

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
        $response = $controller->{$this->controllerInfo['action_name']}(
            isset($this->controllerInfo['params'])
                ? $this->controllerInfo['params']
                : null
        );
        $response->send();
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
     * Returns true, if a POST variable exists.
     *
     * @param string $key POST variable
     * @return bool true, if POST variable exists
     */
    public function hasPostParam($key)
    {
        return isset($this->requestInfo['post_params'])
            && isset($this->requestInfo['post_params'][$key]);
    }

    /**
     * Returns a POST variable, if available
     *
     * @param string $key POST variable name to return
     * @return null|string|array value of POST variable or null if not available
     */
    public function getPostParam($key)
    {
        if (!$this->hasPostParam($key)) {
            return null;
        }

        return $this->requestInfo['post_params'][$key];
    }

    /**
     * Alters a POST variable, if available.
     *
     * @param string $key POST variable name to alter
     * @param string|array $value POST variable value to set
     */
    public function alterPostParam($key, $value)
    {
        if (!$this->hasPostParam($key)) {
            return;
        }

        $this->requestInfo['post_params'][$key] = $value;
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

    /**
     * Executes a cURL request and outputs the response.
     *
     * @param string $url URL to request
     * @param string $method HTTP method ("get", "post", "delete", "put")
     * @param null|array $postFields POST parameters
     * @return string HTTP output of response
     */
    public function curlRequest($url, $method, array $postFields = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt(
            $curl,
            CURLOPT_USERAGENT,
            str_replace(
                array('{url}', '{mail}'),
                array(
                    App::instance()->getConfig('url'),
                    App::instance()->getConfig('mail')
                ),
                App::instance()->getConfig('useragent')
            )
        );

        // If we have values to be posted (possible in POST, DELETE, PUT),
        // raw encode the POST values by using "http_build_query()".
        if (is_array($postFields) && count($postFields) > 0) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
        }

        $curlResponse = curl_exec($curl);
        curl_close($curl);

        return $curlResponse . PHP_EOL;
    }
}
