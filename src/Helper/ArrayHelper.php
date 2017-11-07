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


class ArrayHelper
{
    /**
     * @var ArrayHelper|null instance
     */
    protected static $instance = null;

    /**
     * ArrayHelper constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton.
     * @return ArrayHelper instance
     */
    public static function instance()
    {
        if (!isset(ArrayHelper::$instance)) {
            ArrayHelper::$instance = new ArrayHelper();
        }

        return ArrayHelper::$instance;
    }

    /**
     * Returns a value by a path from an array.
     * E.g.: getValueByPath(
     *  array('a' => array('b' => 'c')),
     *  'a/b'
     * ) returns 'c'.
     *
     * @param array $array array to return the value
     * @param string $path path in array
     * @return mixed Value or null
     */
    public function getValueByPath($array, $path)
    {
        $explodedPath = explode('/', $path);
        $value = $array;
        foreach ($explodedPath as $key) {
            $value = $value[$key];
        }

        return $value;
    }
}
