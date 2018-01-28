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


class SvgResponse extends Response
{
    /**
     * SvgResponse constructor.
     *
     * @param mixed $content content
     * @param string $fileName SVG filename
     * @param int $httpStatus HTTP status code
     * @param array $customParameters custom parameters
     */
    public function __construct(
        $content = null,
        $fileName = 'file.svg',
        $httpStatus = self::HTTP_STATUS_OK,
        array $customParameters = null
    ) {
        $customParameters['Content-Disposition'] = 'attachment; filename="' . $fileName . '"';
        parent::__construct(
            $content,
            $httpStatus,
            Response::CONTENT_TYPE_SVG,
            $customParameters
        );
    }
}
