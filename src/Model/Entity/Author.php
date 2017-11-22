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


use BS\Helper\DataTypeHelper;
use BS\Model\Db\Database;

/**
 * Class Author.
 *
 * @package BS\Model\Entity
 * @method int|string|null getId()
 * @method string|null getFirstName()
 * @method string|null getLastName()
 */
class Author extends Entity
{
    /**
     * @var int|null $id author identifier
     */
    protected $id = null;

    /**
     * @var string|null $firstName first name of author
     */
    protected $firstName = null;

    /**
     * @var string|null $lastName last name of author
     */
    protected $lastName = null;

    /**
     * Author constructor.
     *
     * @param int|null $id author identifier
     * @param string|null $firstName first name of author
     * @param string|null $lastName last name of author
     */
    public function __construct(
        $id = null,
        $firstName = null,
        $lastName = null
    ) {
        parent::__construct();
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * Reads all ($id = null) or a specific entity and returns it.
     *
     * @param int|string|null $id identifier of this entity
     * @return IEntity|array|null IEntity instance(s) or null
     */
    public static function read($id = null)
    {
        $sql = 'SELECT a.id_author, a.first_name, a.last_name FROM author a';
        $sqlParams = array();

        if ($id !== null) {
            if (static::isInCache($id)) {
                return static::getCache($id);
            }

            $sql .= ' WHERE a.id_author = ?';
            $sqlParams[] = $id;
        }

        // Fetch result from the database.
        $sqlResult = Database::instance()->select($sql, $sqlParams);

        // If we have no result, return null.
        if (count($sqlResult) == 0) {
            return null;
        }

        // We have at least one result, create project entity/entities.
        foreach ($sqlResult as $record) {
            $author = new Author(
                DataTypeHelper::instance()->get($record['a.id_author'], 'int'),
                $record['a.first_name'],
                $record['a.last_name']
            );
            static::addToCache($author);
        }

        return $id !== null
            ? static::getCache($id)
            : static::getCache();
    }

    /**
     * Creates an entity in the database. Performs INSERT statement.
     */
    public function create()
    {
        // If this entity already has an ID, don't perform insert.
        if (isset($this->id)) {
            return;
        }

        $sql = 'INSERT INTO author (first_name, last_name)
                VALUES (?, ?)';

        $sqlParams = array(
            $this->firstName,
            $this->lastName
        );

        $this->id = Database::instance()->insert($sql, $sqlParams);
    }

    /**
     * Updates an entity in the database. Performs UPDATE statement.
     */
    public function update()
    {
        // If no ID is set, this entity is not in the database already.
        if (!isset($this->id)) {
            return;
        }

        $sql = 'UPDATE author SET first_name = ?, last_name = ?
                WHERE id_author = ?';
        $sqlParams = array(
            $this->firstName,
            $this->lastName,
            $this->id
        );

        Database::instance()->updateOrDelete($sql, $sqlParams);
    }

    /**
     * Deletes an entity in the database Performs DELETE statement.
     */
    public function delete()
    {
        // If no ID is set, this entity is not in the database already.
        if (!isset($this->id)) {
            return;
        }

        $sql = 'DELETE FROM author WHERE id_author = ?';
        $sqlParams = array($this->id);

        Database::instance()->updateOrDelete($sql, $sqlParams);
        $this->id = null;
    }
}
