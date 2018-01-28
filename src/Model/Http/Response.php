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


class Response
{
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_NO_CONTENT = 204;
    const HTTP_STATUS_REDIRECT = 302;
    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_SERVER_ERROR = 500;
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_SVG = 'image/svg+xml';

    /**
     * @var string $contentType HTTP content type
     */
    protected $contentType = null;

    /**
     * @var int $httpStatus HTTP status code
     */
    protected $httpStatus = null;

    /**
     * @var string $content content
     */
    protected $content = null;

    /**
     * @var null|array $customParameters custom parameters
     */
    protected $customParameters = null;

    /**
     * Getter custom parameters.
     *
     * @return array|null custom parameters
     */
    public function getCustomParameters()
    {
        return $this->customParameters;
    }

    /**
     * Setter custom parameters.
     *
     * @param array $customParameters custom parameters
     */
    public function setCustomParameters(array $customParameters)
    {
        $this->customParameters = $customParameters;
    }

    /**
     * Getter HTTP content type.
     *
     * @return string HTTP content type
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Setter HTTP content type.
     *
     * @param string $contentType HTTP content type
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Getter HTTP status code.
     *
     * @return int HTTP status code
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Setter HTTP status code.
     *
     * @param int $httpStatus HTTP status code
     */
    public function setHttpStatus($httpStatus)
    {
        $this->httpStatus = $httpStatus;
    }

    /**
     * Getter content.
     *
     * @return string content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Setter content.
     *
     * @param string $content Content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Response constructor.
     *
     * @param string $content content
     * @param int $httpStatus HTTP status code
     * @param string $contentType HTTP content type
     * @param array $customParameters custom parameters
     */
    public function __construct(
        $content = '',
        $httpStatus = self::HTTP_STATUS_OK,
        $contentType = self::CONTENT_TYPE_HTML,
        array $customParameters = null
    ) {
        $this->content = $content;
        $this->httpStatus = $httpStatus;
        $this->contentType = $contentType;
        $this->customParameters = $customParameters;
    }

    /**
     * Sends the HTTP response and exits the application.
     */
    public function send()
    {
        \http_response_code($this->httpStatus);
        header('Content-Type: ' . $this->contentType);

        if (is_array($this->customParameters)) {
            foreach ($this->customParameters as $parameterKey => $parameterValue) {
                header(
                    (string)$parameterKey . ': '
                    . (string)$parameterValue
                );
            }
        }

        echo $this->content;
        exit();
    }
}
