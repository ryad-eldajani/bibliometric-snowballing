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

namespace BS\Model\Api;


use BS\Model\App;
use BS\Model\Exceptions\AppException;
use BS\Model\Http\Http;

abstract class AbstractApi implements IApi
{
    /**
     * @var array<string|AbstractApi> $instance name -> AbstractApi instance
     */
    protected static $instances = array();

    /**
     * @var string|null $configuredUrl configured URL to with placeholders
     */
    protected $configuredUrl = null;

    /**
     * @var string|null $method HTTP request method
     */
    protected $method = null;

    /**
     * @var array|null $postFields POST fields for HTTP request
     */
    protected $postFields = null;

    /**
     * @var Http|null $http Http instance
     */
    protected $http = null;

    /**
     * Returns a new AbstractApi instance.
     *
     * @param string $api API name
     * @return AbstractApi instance
     * @throws AppException
     */
    protected static function getApiInstance($api)
    {
        $apiConfig = App::instance()->getConfig('apis/' . $api);
        /** @var AbstractApi $apiClass */
        $apiClass = $apiConfig['class'];

        if (!class_exists($apiClass)) {
            throw new AppException('API class "' . $apiClass . '" does not exist.');
        }

        return new $apiClass(
            $apiConfig['url'],
            $apiConfig['method']
        );
    }

    /**
     * Returns an AbstractApi instance by name.
     *
     * @param string $api API name
     * @return AbstractApi AbstractApi instance
     */
    public static function instance($api)
    {
        if (!isset(self::$instances[$api])) {
            self::$instances[$api] = self::getApiInstance($api);
        }

        return self::$instances[$api];
    }

    /**
     * AbstractApi constructor.
     *
     * @param string $url URL to request
     * @param string $method HTTP request method
     * @param array|null $postFields POST fields for HTTP request
     */
    private function __construct($url, $method = 'get', array $postFields = null)
    {
        $this->configuredUrl = $url;
        $this->method = $method;
        $this->http = Http::instance();
        $this->postFields = $postFields;
    }

    /**
     * Requests the API.
     *
     * @param string $url URL to request
     * @return string HTTP response
     */
    protected function request($url)
    {
        return $this->http->curlRequest(
            $url,
            $this->method,
            $this->postFields
        );
    }
}
