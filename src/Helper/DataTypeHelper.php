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


class DataTypeHelper
{
    /**
     * @var DataTypeHelper|null instance
     */
    protected static $instance = null;

    /**
     * DataTypeHelper constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton.
     * @return DataTypeHelper instance
     */
    public static function instance()
    {
        if (!isset(DataTypeHelper::$instance)) {
            DataTypeHelper::$instance = new DataTypeHelper();
        }

        return DataTypeHelper::$instance;
    }

    /**
     * Returns a variable converted to desired data type from a string.
     *
     * @param string $item string to be converted
     * @param string $dataType desired data type
     * @param bool $strict if true string must be valid
     * @return bool|mixed variable if converted, false if strict and not valid
     */
    public function get($item, $dataType = 'int', $strict = true)
    {
        if ($dataType == 'int' && is_int($item)) {
            return $item;
        } elseif ($dataType == 'double' && is_double($item)) {
            return $item;
        } elseif ($dataType == 'bool' && is_bool($item)) {
            return $item;
        }

        if ($dataType == 'int') {
            if ($strict) {
                return filter_var($item, FILTER_VALIDATE_INT);
            }
            return intval($item);
        } elseif ($dataType == 'double') {
            if ($strict) {
                return filter_var($item, FILTER_VALIDATE_FLOAT);
            }
            return floatval($item);
        } elseif ($dataType == 'bool') {
            if ($strict) {
                return filter_var($item, FILTER_VALIDATE_BOOLEAN);
            }
            return boolval($item);
        }

        return false;
    }

    /**
     * Returns an array with converted variables to desired data types
     * from strings or null.
     *
     * @param string[]|null $items strings to be converted
     * @param string $dataType desired data types
     * @param bool $strict if true strings must be valid
     * @return bool|mixed array of variables if converted
     */
    public function getArray($items = null, $dataType = 'int', $strict = true)
    {
        if ($items === null || (count($items) == 1 && $items[0] == '')) {
            return null;
        }

        $convertedItems = array();

        foreach ($items as $item) {
            $convertedItems[] = $this->get($item, $dataType, $strict);
        }

        return $convertedItems;
    }
}
