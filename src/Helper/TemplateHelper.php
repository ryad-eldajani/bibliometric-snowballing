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

namespace BS\Helper;


use BS\Model\Http\Http;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class TemplateHelper implements ExtensionInterface
{
    /**
     * Registers template engine functions.
     *
     * @param Engine $engine template engine instance
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('active', [$this, 'tabActive']);
    }

    /**
     * Returns the string 'class="active"' if the path matches the request
     * path.
     *
     * @param string $path path to check
     * @param bool $withoutClass if true, 'class=' is omitted
     * @return string 'class="active"' is path matches
     */
    public function tabActive($path, $withoutClass = false)
    {
        return Http::instance()->getRequestInfo('path') == $path
            ? ($withoutClass ? ' active' : ' class="active"')
            : '';
    }
}
