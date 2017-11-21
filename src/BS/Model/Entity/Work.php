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
 * Class Work.
 * @package BS\Model\Entity
 * @method int|null getId()
 * @method string|null getTitle()
 * @method string|null getSubTitle()
 * @method int|null getWorkYear()
 * @method string|null getDoi()
 */
class Work extends Entity
{
    /**
     * @var int|null $id work identifier
     */
    protected $id = null;

    /**
     * @var string|null $title work title
     */
    protected $title = null;

    /**
     * @var string|null $subTitle work subtitle
     */
    protected $subTitle = null;

    /**
     * @var int|null $workYear year of this work
     */
    protected $workYear = null;

    /**
     * @var string|null $doi document object identifier (DOI) of this work
     */
    protected $doi = null;

    /**
     * Work constructor.
     *
     * @param int|null $id work identifier
     * @param string|null $title work title
     * @param string|null $subTitle work subtitle
     * @param int|null $workYear year of this work
     * @param string|null $doi document object identifier (DOI) of this work
     */
    public function __construct(
        $id = null,
        $title = null,
        $subTitle = null,
        $workYear = null,
        $doi = null
    ) {
        parent::__construct();
        $this->id = $id;
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->workYear = $workYear;
        $this->doi = $doi;
    }

    /**
     * Reads all ($id = null) or a specific entity and returns it.
     *
     * @param int|string|null $id identifier of this entity
     * @return IEntity|array|null IEntity instance(s) or null
     */
    public static function read($id = null)
    {
        $sql = 'SELECT * FROM';
        $sqlParams = array();

        if ($id !== null) {
            if (self::isInCache($id)) {
                return self::getCache($id);
            }

            $sql .= '  work WHERE id_work = ?';
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
            $work = new Work(
                DataTypeHelper::instance()->get($record['id_work'], 'int'),
                $record['title'],
                $record['subtitle'],
                DataTypeHelper::instance()->get($record['work_year'], 'int'),
                $record['doi']
            );
            self::addToCache($work);
        }

        return $id !== null
            ? self::getCache($id)
            : self::getCache();
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

        $sql = 'INSERT INTO work (title, subtitle, work_year, doi)
                VALUES (?, ?, ?, ?)';

        $sqlParams = array(
            $this->title,
            $this->subTitle,
            $this->workYear,
            $this->doi
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

        $sql = 'UPDATE work SET title = ?, subtitle = ?, work_year = ?, doi = ?
                WHERE id_work = ?';
        $sqlParams = array(
            $this->title,
            $this->subTitle,
            $this->workYear,
            $this->doi,
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

        $sql = 'DELETE FROM work WHERE id_work = ?';
        $sqlParams = array($this->id);

        Database::instance()->updateOrDelete($sql, $sqlParams);
        $this->id = null;
    }
}
