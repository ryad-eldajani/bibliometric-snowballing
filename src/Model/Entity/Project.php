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
use BS\Model\User\UserManager;

/**
 * Class Project.
 *
 * @package BS\Model\Entity
 * @method int|string|null getId()
 * @method string|null getName()
 * @method int|null getUserId()
 * @method int|null getCreatedAt()
 * @method int[] getWorkIds()
 * @method array<int|Work> getWorks()
 * @method void setName(string $name)
 * @method void setUserId(int $userId)
 * @method void setWorkIds(int[] $workIds)
 * @method void setWorks(array $works)
 */
class Project extends Entity
{
    /**
     * @var int|null $id project identifier
     */
    protected $id = null;

    /**
     * @var string|null $name project name
     */
    protected $name = null;

    /**
     * @var int|null $userId user identifier
     */
    protected $userId = null;

    /**
     * @var int|null $createdAt creation timestamp
     */
    protected $createdAt = null;

    /**
     * @var int[] array of work identifiers
     */
    protected $workIds = array();

    /**
     * @var array<int|Work> list of ID => Work entities
     */
    protected $works = array();

    /**
     * Project constructor.
     *
     * @param int|null $id project identifier
     * @param string|null $name project name
     * @param string|null $createdAt creation timestamp
     * @param int|null $userId user identifier
     * @param int[] $workIds array of work identifiers
     */
    public function __construct(
        $id = null,
        $name = null,
        $createdAt = null,
        $userId = null,
        array $workIds = array()
    ) {
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->workIds = $workIds;
    }

    /**
     * Reads all ($id = null) or a specific entity and returns it.
     *
     * @param int|string|null $id identifier of this entity
     * @return Project|array|null Project instance(s) or null
     */
    public static function read($id = null)
    {
        $sql = 'SELECT p.id_project, p.project_name,
                UNIX_TIMESTAMP(p.created_at) as created_at, p.id_user,
                 (SELECT GROUP_CONCAT(wp.id_work) FROM work_project wp
                  WHERE wp.id_project = p.id_project) AS work_ids
                FROM project p WHERE p.id_user = ?';
        $sqlParams = array(UserManager::instance()->getUserParam('uid'));

        if ($id !== null) {
            if (static::isInCache($id)) {
                return static::getCache($id);
            }

            $sql .= ' AND p.id_project = ?';
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
            $project = new Project(
                DataTypeHelper::instance()->get($record['id_project'], 'int'),
                $record['project_name'],
                DataTypeHelper::instance()->get($record['created_at'], 'int'),
                DataTypeHelper::instance()->get($record['id_user'], 'int'),
                DataTypeHelper::instance()->getArray(explode(',', $record['work_ids']), 'int')
            );
            static::addToCache($project);
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

        $sql = 'INSERT INTO project (id_user, project_name) VALUES (?, ?)';
        $sqlParams = array($this->userId, $this->name);

        $this->id = Database::instance()->insert($sql, $sqlParams);
        static::addToCache($this);
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

        $sql = 'UPDATE project SET id_user = ?, project_name = ? WHERE id_project = ?';
        $sqlParams = array($this->userId, $this->name, $this->id);
        Database::instance()->updateOrDelete($sql, $sqlParams);

        // Update work IDs.
        foreach ($this->workIds as $workId) {
            $sql = 'REPLACE INTO work_project (id_project, id_work) VALUES (?, ?)';
            $sqlParams = array($this->id, $workId);
            Database::instance()->updateOrDelete($sql, $sqlParams);
        }
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

        // Delete work_project references.
        $sql = 'DELETE FROM work_project WHERE id_project = ?';
        $sqlParams = array($this->id);
        Database::instance()->updateOrDelete($sql, $sqlParams);

        // Delete project itself.
        $sql = 'DELETE FROM project WHERE id_project = ?';
        $sqlParams = array($this->id);
        Database::instance()->updateOrDelete($sql, $sqlParams);

        $this->id = null;
        $this->workIds = array();
        $this->works = array();
    }

    /**
     * Returns the work entities as a ID -> IEntity list.
     *
     * @return array|null array of entities
     */
    public function getWorks()
    {
        // If no ID is set, this entity does not exist in the database.
        if (!isset($this->id)) {
            return null;
        }

        // If works are already fetched, return.
        if (count($this->workIds) == count($this->works)) {
            return $this->works;
        }

        $sql = 'SELECT * FROM work w, work_project wp
                WHERE w.id_work = wp.id_work AND wp.id_project = ?';
        $sqlParams = array($this->id);

        $sqlResult = Database::instance()->select($sql, $sqlParams);

        if (count($sqlResult) == 0) {
            return null;
        }

        $this->works = array();
        foreach ($sqlResult as $record) {
            $workId = DataTypeHelper::instance()->get($record['id_work'], 'int');

            // If work entity is already in cache, use entity from cache.
            if ($work = Work::getCache($workId)) {
                $this->works[(string)$workId] = $work;
                continue;
            }

            $work = new Work(
                $workId,
                $record['title'],
                $record['subtitle'],
                DataTypeHelper::instance()->get($record['work_year'], 'int'),
                $record['doi']
            );
            Work::addToCache($work);
            $this->works[(string)$workId] = $work;
        }

        return $this->works;
    }

    /**
     * Adds a work identifier.
     *
     * @param int $workId work identifier
     */
    public function addWorkId($workId)
    {
        if (!$this->hasWorkId($workId)) {
            $this->workIds[] = $workId;
        }
    }

    /**
     * Returns true, if a work ID is already given.
     *
     * @param int $workId work ID to check.
     * @return bool true, if work ID is given
     */
    public function hasWorkId($workId)
    {
        return in_array($workId, $this->workIds);
    }
}
