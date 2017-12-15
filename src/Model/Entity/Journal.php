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
 * Class Journal.
 *
 * @package BS\Model\Entity
 * @method int|string|null getId()
 * @method string getJournalName()
 * @method string getIssn()
 */
class Journal extends Entity
{
    /**
     * @var int|null $id journal identifier
     */
    protected $id = null;

    /**
     * @var string $journalName journal name
     */
    protected $journalName = '';

    /**
     * @var string $issn ISSN of journal
     */
    protected $issn = '';

    /**
     * Author constructor.
     *
     * @param int|null $id journal identifier
     * @param string $journalName journal name
     * @param string $issn ISSN of journal
     */
    public function __construct(
        $id = null,
        $journalName = '',
        $issn = ''
    ) {
        parent::__construct();
        $this->id = $id;
        $this->journalName = $journalName;
        $this->issn = $issn;
    }

    /**
     * Reads all ($id = null) or a specific entity and returns it.
     *
     * @param int|string|null $id identifier of this entity
     * @return IEntity|array|null IEntity instance(s) or null
     */
    public static function read($id = null)
    {
        $sql = 'SELECT j.id_journal, j.journal_name, j.issn FROM journal j';
        $sqlParams = array();

        if ($id !== null) {
            if (static::isInCache($id)) {
                return static::getCache($id);
            }

            $sql .= ' WHERE j.id_journal = ?';
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
            $journal = new Journal(
                DataTypeHelper::instance()->get($record['id_journal'], 'int'),
                $record['journal_name'],
                $record['issn']
            );
            static::addToCache($journal);
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

        $sql = 'INSERT INTO journal (journal_name, issn)
                VALUES (?, ?)';

        $sqlParams = array(
            $this->journalName,
            $this->issn
        );

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

        $sql = 'UPDATE journal SET journal_name = ?, issn = ?
                WHERE id_journal = ?';
        $sqlParams = array(
            $this->journalName,
            $this->issn,
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

        $sql = 'DELETE FROM journal WHERE id_journal = ?';
        $sqlParams = array($this->id);

        Database::instance()->updateOrDelete($sql, $sqlParams);
        $this->id = null;
    }

    /**
     * Reads an entity by ISSN and returns it.
     *
     * @param string $issn first name of this entity
     * @return Journal|null instance or null
     */
    public static function readByIssn($issn)
    {
        $sql = 'SELECT j.id_journal, j.journal_name, j.issn FROM journal j
                WHERE j.issn = ?';
        $sqlParams = array($issn);

        // Fetch result from the database.
        $sqlResult = Database::instance()->select($sql, $sqlParams);

        // If we have no result, return null.
        if (count($sqlResult) != 1) {
            return null;
        }

        $journal = new Journal(
            DataTypeHelper::instance()->get($sqlResult[0]['id_journal'], 'int'),
            $sqlResult[0]['journal_name'],
            $sqlResult[0]['issn']
        );
        static::addToCache($journal);

        return $journal;
    }
}
