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

namespace BS\Model\Entity;


/**
 * Abstract class Entity.
 *
 * @package BS\Model\Entity
 */
abstract class Entity implements IEntity, \JsonSerializable
{
    /**
     * @var array<int|string,IEntity> $cache entity cache
     */
    protected static $cache = array();

    /**
     * Entity constructor.
     */
    public function __construct()
    {
    }

    /**
     * Magic method __get() to return a value for an unavailable property.
     *
     * @param string $name property name
     * @return null|mixed property value
     */
    public function __get($name)
    {
        $properties = get_object_vars($this);

        if (!isset($properties[$name])) {
            return null;
        }

        return $properties[$name];
    }

    /**
     * Magic method __get() to return a value for an unavailable property.
     *
     * @param string $name property name
     * @param array $value property value
     */
    public function __set($name, $value)
    {
        $properties = get_object_vars($this);

        if (!isset($properties[$name]) || count($value) != 1) {
            return;
        }

        $this->$name = $value[0];
    }

    /**
     * Magic method __call() to get "magic getters".
     * E.g. $this->getId() returns $this->id.
     *
     * @param string $name method name
     * @param array $arguments method arguments
     * @return mixed|null property
     */
    public function __call($name, $arguments)
    {
        $nameParts = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (count($nameParts) < 2) {
            return null;
        }

        if (!in_array($nameParts[0], array('get', 'set'))) {
            return null;
        }

        $propertyName = '';
        for ($i = 1; $i < count($nameParts); $i++) {
            $propertyName .= $i == 1
                ? strtolower($nameParts[$i])
                : $nameParts[$i];
        }

        return $nameParts[0] == 'get'
            ? $this->__get($propertyName)
            : $this->__set($propertyName, $arguments);
    }

    /**
     * Returns true, if an  IEntity instance is in cache.
     *
     * @param int|string $id identifier of the entity
     * @return bool true, if IEntity with $id exists
     */
    public static function isInCache($id)
    {
        return isset(self::$cache[(string)$id]);
    }

    /**
     * Returns the an IEntity instance from the cache
     * or the complete cache ($id = null). If an $id
     * is given and the entity is not in the cache,
     * it will be retrieved using the self::read() method.
     *
     * @param int|string|null $id IEntity identifier
     * @return IEntity|array IEntity instance(s) from cache
     */
    public static function getCache($id = null)
    {
        return $id !== null
            ? (
                self::isInCache($id)
                ? self::$cache[$id]
                : self::read($id)
            )
            : self::$cache;
    }

    /**
     * Adds an IEntity instance to the cache.
     *
     * @param IEntity $entity IEntity instance
     */
    public static function addToCache(IEntity $entity)
    {
        self::$cache[(string)$entity->getId()] = $entity;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return \json_encode(get_object_vars($this));
    }
}
