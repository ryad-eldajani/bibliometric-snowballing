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


class RedirectResponse extends Response
{
    /**
     * @var string $redirectPath URL path to redirect to
     */
    protected $redirectPath = null;

    /**
     * RedirectResponse constructor.
     *
     * @param string $redirectPath URL to redirect to
     * @param array $customParameters custom parameters
     */
    public function __construct($redirectPath, array $customParameters = null) {
        parent::__construct(
            '',
            self::HTTP_STATUS_REDIRECT,
            self::CONTENT_TYPE_HTML,
            $customParameters
        );
        $this->redirectPath = $redirectPath;
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

        header(
            'Location: '
            . Http::instance()->getRequestInfo('base_path')
            . $this->redirectPath
        );
        exit();
    }
}
