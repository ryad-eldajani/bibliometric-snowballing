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


abstract class Entity implements IEntity, \JsonSerializable
{
    /**
     * @var array $cache entity cache
     */
    protected static $cache = array();

    /**
     * Entity constructor.
     */
    public function __construct()
    {
    }

    /**
     * Returns a value for an attribute by key or null if not existent.
     *
     * @param string $attribute attribute name
     * @return null|mixed attribute value or null
     */
    public function get($attribute)
    {
        $properties = get_object_vars($this);

        if (!isset($properties[$attribute])) {
            return null;
        }

        return $properties[$attribute];
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
        self::$cache[(string)$entity->get('id')] = $entity;
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
