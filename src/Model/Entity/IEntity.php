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


interface IEntity
{
    /**
     * Reads all ($id = null) or a specific entity and returns it.
     *
     * @param int|string|null $id identifier of this entity
     * @return IEntity|array|null IEntity instance(s) or null
     */
    public static function read($id = null);

    /**
     * Creates an entity in the database. Performs INSERT statement.
     */
    public function create();

    /**
     * Updates an entity in the database. Performs UPDATE statement.
     */
    public function update();

    /**
     * Deletes an entity in the database Performs DELETE statement.
     */
    public function delete();

    /**
     * Returns a value for an attribute by key or null if not existent.
     *
     * @param string $attribute attribute name
     * @return null|mixed attribute value or null
     */
    public function get($attribute);

    /**
     * Returns the an IEntity instance from the cache
     * or the complete cache ($id = null). If an $id
     * is given and the entity is not in the cache,
     * it will be retrieved using the self::read() method.
     *
     * @param int|string|null $id IEntity identifier
     * @return IEntity|array IEntity instance(s) from cache
     */
    public static function getCache($id = null);

    /**
     * Returns true, if an  IEntity instance is in cache.
     *
     * @param int|string $id identifier of the entity
     * @return bool true, if IEntity with $id exists
     */
    public static function isInCache($id);

    /**
     * Adds an IEntity instance to the cache.
     *
     * @param IEntity $entity IEntity instance
     */
    public static function addToCache(IEntity $entity);
}
