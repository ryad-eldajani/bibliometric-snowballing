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


class JsonResponse extends Response
{
    /**
     * JsonResponse constructor.
     *
     * @param mixed $content content
     * @param int $httpStatus HTTP status code
     * @param array $customParameters custom parameters
     */
    public function __construct(
        $content = null,
        $httpStatus = self::HTTP_STATUS_OK,
        array $customParameters = null
    ) {
        parent::__construct(
            json_encode($content),
            $httpStatus,
            Response::CONTENT_TYPE_JSON,
            $customParameters
        );
    }
}
