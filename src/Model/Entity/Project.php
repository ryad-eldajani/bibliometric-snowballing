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
     * @var string|null $createdAt creation timestamp
     */
    protected $createdAt = null;

    /**
     * @var int[]|null array of work identifiers
     */
    protected $workIds = null;

    /**
     * Project constructor.
     *
     * @param int|null $id project identifier
     * @param string|null $name project name
     * @param string|null $createdAt creation timestamp
     * @param int|null $userId user identifier
     * @param null|array $workIds
     */
    public function __construct(
        $id = null,
        $name = null,
        $createdAt = null,
        $userId = null,
        $workIds = null
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
     * @return IEntity|array|null IEntity instance(s) or null
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
            if (self::isInCache($id)) {
                return self::getCache($id);
            }

            $sql .= ' AND p.id_project = ?';
            $sqlParams[] = $id;
        }

        $sqlResult = Database::instance()->select($sql, $sqlParams);

        if (count($sqlResult) == 0) {
            return null;
        }

        foreach ($sqlResult as $record) {
            $project = new Project(
                DataTypeHelper::instance()->get($record['id_project'], 'int'),
                $record['project_name'],
                DataTypeHelper::instance()->get($record['created_at'], 'int'),
                DataTypeHelper::instance()->get($record['id_user'], 'int'),
                DataTypeHelper::instance()->getArray(explode(',', $record['work_ids']), 'int')
            );
            self::addToCache($project);
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
        // TODO: Implement create() method.
    }

    /**
     * Updates an entity in the database. Performs UPDATE statement.
     */
    public function update()
    {
        // TODO: Implement update() method.
    }

    /**
     * Deletes an entity in the database Performs DELETE statement.
     */
    public function delete()
    {
        // TODO: Implement delete() method.
    }
}
