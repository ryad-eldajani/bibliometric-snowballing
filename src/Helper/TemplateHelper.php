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


use BS\Model\Entity\Entity;
use BS\Model\Http\Http;
use BS\Model\User\UserManager;
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
        $engine->registerFunction('date', [$this, 'dateFormat']);
        $engine->registerFunction('join', [$this, 'joinArray']);
        $engine->registerFunction('joinEntities', [$this, 'joinEntities']);
        $engine->registerFunction('parseMarkdown', [$this, 'parseMarkdown']);
        $engine->registerFunction('postParam', [$this, 'getHttpPostParam']);
        $engine->registerFunction('userParam', [$this, 'getUserParam']);
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

    /**
     * Formats a date.
     *
     * @param int $timestamp timestamp
     * @param string $format date format
     * @return string formatted date
     */
    public function dateFormat($timestamp, $format = 'd.m.Y')
    {
        return date($format, $timestamp);
    }

    /**
     * Joins an array to a string.
     * E.g.: joinArray(array('a', 'b', 'c'), ', ') -> 'a, b, c'
     *
     * @param array $array array to join
     * @param string $separator separator to use
     * @return string joined stirng
     */
    public function joinArray(array $array = null, $separator = ', ')
    {
        return $array !== null ? implode($separator, $array) : '';
    }

    /**
     * Joins an array of entites with properties.
     *
     * @param array|null $entities entities
     * @param array $properties properties
     * @param string $separator separator
     * @return string joined string
     */
    public function joinEntities(array $entities = null, array $properties = array(), $separator = ', ')
    {
        if (!is_array($entities)) {
            return '';
        }

        $output = '';
        $i = 0;
        foreach ($entities as $entity) {
            /** @var Entity $entity */

            if ($i++ > 0) {
                $output .= $separator;
            }

            $j = 0;
            foreach ($properties as $property) {
                if ($j++ > 0) {
                    $output .= ' ';
                }
                $output .= $entity->__get($property);
            }
        }

        return $output;
    }

    /**
     * Parses markdown content.
     *
     * @param string $markdown markdown content
     * @return string parsed markdown
     */
    public function parseMarkdown($markdown)
    {
        return (new \parsedown\Parsedown())->text($markdown);
    }

    /**
     * Returns a HTTP POST parameter value as string.
     *
     * @param string $param HTTP POST parameter key
     * @return string HTTP POST parameter value or empty string
     */
    public function getHttpPostParam($param)
    {
        return Http::instance()->hasPostParam($param)
            ? Http::instance()->getPostParam($param)
            : '';
    }

    /**
     * Returns a user parameter if available.
     *
     * @param string $param user parameter key
     * @return string user parameter value or empty string
     */
    public function getUserParam($param)
    {
        $value = UserManager::instance()->getUserParam($param);
        return isset($value) ? $value : '';
    }
}
