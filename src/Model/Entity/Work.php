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
use BS\Model\Api\AbstractApi;
use BS\Model\Api\CrossRefApi;
use BS\Model\Db\Database;

/**
 * Class Work.
 *
 * @package BS\Model\Entity
 * @method int|string|null getId()
 * @method string|null getTitle()
 * @method string|null getSubTitle()
 * @method int|null getWorkYear()
 * @method string|null getDoi()
 * @method int[] getAuthorIds()
 * @method int[] getJournalIds()
 * @method int[] getWorkDois()
 * @method void setDoi(string $doi)
 * @method void setTitle(string $title)
 * @method void setSubTitle(string $title)
 * @method void setWorkYear(int $year)
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
     * @var int[] array of author identifiers
     */
    protected $authorIds = null;

    /**
     * @var int[] array of journal identifiers
     */
    protected $journalIds = null;

    /**
     * @var string[] array of referenced work DOIs
     */
    protected $workDois = array();

    /**
     * @var array<int|Author> $authors list of ID => Author entities
     */
    protected $authors = array();

    /**
     * @var array<int|Journal> $journals list of ID => Journal entities
     */
    protected $journals = array();

    /**
     * Work constructor.
     *
     * @param int|null $id work identifier
     * @param string|null $title work title
     * @param string|null $subTitle work subtitle
     * @param int|null $workYear year of this work
     * @param string|null $doi document object identifier (DOI) of this work
     * @param int[] $authorIds array of author identifiers
     * @param int[] $journalIds array of journal identifiers
     * @param int[] $workDois array of work DOIs
     */
    public function __construct(
        $id = null,
        $title = null,
        $subTitle = null,
        $workYear = null,
        $doi = null,
        array $authorIds = array(),
        array $journalIds = array(),
        array $workDois = array()
    ) {
        parent::__construct();
        $this->id = $id;
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->workYear = $workYear;
        $this->doi = $doi;
        $this->authorIds = $authorIds;
        $this->journalIds = $journalIds;
        $this->workDois = $workDois;
    }

    /**
     * Reads all ($id = null) or a specific entity and returns it.
     *
     * @param int|string|null $id identifier of this entity
     * @return Work|array|null Work instance(s) or null
     */
    public static function read($id = null)
    {
        $sql = 'SELECT w.id_work, w.title, w.subtitle, w.work_year, w.doi,
                  (SELECT GROUP_CONCAT(wj.id_journal) FROM work_journal wj WHERE wj.id_work = w.id_work)
                  AS journal_ids,
                  (SELECT GROUP_CONCAT(wa.id_author) FROM work_author wa WHERE wa.id_work = w.id_work)
                  AS author_ids,
                  (SELECT GROUP_CONCAT(LOWER(q.doi_work_quoted)) FROM quote q WHERE q.doi_work = w.doi)
                  AS work_dois
                FROM work w';
        $sqlParams = array();

        if ($id !== null) {
            if (static::isInCache($id)) {
                return static::getCache($id);
            }

            $sql .= ' WHERE id_work = ?';
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
                strtolower($record['doi']),
                DataTypeHelper::instance()->getArray(explode(',', $record['author_ids']), 'int'),
                DataTypeHelper::instance()->getArray(explode(',', $record['journal_ids']), 'int'),
                DataTypeHelper::instance()->getArray(explode(',', $record['work_dois']), 'string')
            );
            $work->setAuthorsJournals();
            static::addToCache($work);
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

        $sql = 'INSERT INTO work (title, subtitle, work_year, doi)
                VALUES (?, ?, ?, ?)';

        $sqlParams = array(
            $this->title,
            $this->subTitle,
            $this->workYear,
            $this->doi
        );

        $this->id = Database::instance()->insert($sql, $sqlParams);
        static::addToCache($this);

        // Create author IDs.
        if (count($this->authorIds) > 0) {
            foreach ($this->authorIds as $authorId) {
                $sql = 'INSERT INTO work_author (id_work, id_author) VALUES (?, ?)';
                $sqlParams = array($this->id, $authorId);
                Database::instance()->insert($sql, $sqlParams);
            }
        }

        // Create journal IDs.
        if (count($this->journalIds) > 0) {
            foreach ($this->journalIds as $journalId) {
                $sql = 'INSERT INTO work_journal (id_work, id_journal) VALUES (?, ?)';
                $sqlParams = array($this->id, $journalId);
                Database::instance()->insert($sql, $sqlParams);
            }
        }

        // Create work DOIs.
        if (count($this->workDois) > 0) {
            foreach ($this->workDois as $workDoi) {
                $this->insertDoiReference($workDoi);
            }
        }
    }

    /**
     * Inserts a DOI reference into the database.
     *
     * @param string $doiReference target DOI
     */
    public function insertDoiReference($doiReference)
    {
        if (strtolower($this->doi) == '' || strtolower($doiReference) == '') {
            return;
        }

        $sql = 'INSERT IGNORE INTO quote (doi_work, doi_work_quoted) VALUES (?, ?)';
        $sqlParams = array(strtolower($this->doi), strtolower($doiReference));
        Database::instance()->insert($sql, $sqlParams);
    }

    /**
     * Deletes an author from the database.
     *
     * @param Author $author author entity
     */
    public function deleteAuthor(Author $author)
    {
        $sql = 'DELETE FROM work_author WHERE id_work = ? AND id_author = ?';
        $sqlParams = array($this->id, $author->getId());
        Database::instance()->updateOrDelete($sql, $sqlParams);
        unset($this->authorIds[$author->getId()]);
        unset($this->authors[(string)$author->getId()]);
    }

    /**
     * Deletes a journal from the database.
     *
     * @param Journal $journal author entity
     */
    public function deleteJournal(Journal $journal)
    {
        $sql = 'DELETE FROM work_journal WHERE id_work = ? AND id_journal = ?';
        $sqlParams = array($this->id, $journal->getId());
        Database::instance()->updateOrDelete($sql, $sqlParams);
        unset($this->journalIds[$journal->getId()]);
        unset($this->journals[(string)$journal->getId()]);
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

        // Update author IDs.
        foreach ($this->authorIds as $authorId) {
            $sql = 'REPLACE INTO work_author (id_work, id_author) VALUES (?, ?)';
            $sqlParams = array($this->id, $authorId);
            Database::instance()->updateOrDelete($sql, $sqlParams);
        }

        // Update journal IDs.
        foreach ($this->journalIds as $journalId) {
            $sql = 'REPLACE INTO work_journal (id_work, id_journal) VALUES (?, ?)';
            $sqlParams = array($this->id, $journalId);
            Database::instance()->updateOrDelete($sql, $sqlParams);
        }

        // Update work IDs.
        foreach ($this->workDois as $workDoi) {
            $this->insertDoiReference($workDoi);
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

        // Delete author IDs.
        $sql = 'DELETE FROM work_author WHERE id_work = ?';
        $sqlParams = array($this->id);
        Database::instance()->updateOrDelete($sql, $sqlParams);
        $this->authorIds = null;
        $this->authors = null;

        // Delete journal IDs.
        $sql = 'DELETE FROM work_journal WHERE id_work = ?';
        $sqlParams = array($this->id);
        Database::instance()->updateOrDelete($sql, $sqlParams);
        $this->journalIds = array();
        $this->journals = array();

        $sql = 'DELETE FROM work WHERE id_work = ?';
        $sqlParams = array($this->id);
        Database::instance()->updateOrDelete($sql, $sqlParams);
        $this->id = null;
    }

    /**
     * Returns the author entities as a ID -> IEntity list.
     *
     * @return array<int|Author> array of entities
     */
    public function getAuthors()
    {
        // If no ID is set, this entity does not exist in the database.
        if (!isset($this->id)) {
            return null;
        }

        // If works are already fetched, return.
        if (count($this->authorIds) == count($this->authors)) {
            return $this->authors;
        }

        $sql = 'SELECT * FROM author a, work_author wa
                WHERE a.id_author = wa.id_author AND wa.id_work = ?';
        $sqlParams = array($this->id);

        $sqlResult = Database::instance()->select($sql, $sqlParams);

        if (count($sqlResult) == 0) {
            return null;
        }

        foreach ($sqlResult as $record) {
            $authorId = DataTypeHelper::instance()->get($record['id_author'], 'int');

            // If work entity is already in cache, use entity from cache.
            if ($author = Author::getCache($authorId)) {
                $this->authors[(string)$authorId] = $author;
                continue;
            }

            $author = new Author(
                $authorId,
                $record['first_name'],
                $record['last_name']
            );
            Work::addToCache($author);
            $this->authors[(string)$authorId] = $author;
        }

        return $this->authors;
    }

    /**
     * Returns the author entities as a ID -> IEntity list.
     *
     * @return array<int|Journal> array of entities
     */
    public function getJournals()
    {
        // If no ID is set, this entity does not exist in the database.
        if (!isset($this->id)) {
            return null;
        }

        // If works are already fetched, return.
        if (count($this->journalIds) == count($this->journals)) {
            return $this->journals;
        }

        $sql = 'SELECT * FROM journal j, work_journal wj
                WHERE j.id_journal = wj.id_journal AND wj.id_work = ?';
        $sqlParams = array($this->id);

        $sqlResult = Database::instance()->select($sql, $sqlParams);

        if (count($sqlResult) == 0) {
            return null;
        }

        foreach ($sqlResult as $record) {
            $journalId = DataTypeHelper::instance()->get($record['id_journal'], 'int');

            // If work entity is already in cache, use entity from cache.
            if ($author = Journal::getCache($journalId)) {
                $this->journals[(string)$journalId] = $author;
                continue;
            }

            $author = new Journal(
                $journalId,
                $record['journal_name'],
                $record['issn']
            );
            Journal::addToCache($author);
            $this->journals[(string)$journalId] = $author;
        }

        return $this->journals;
    }

    /**
     * Reads and creates a work entity from CrossRef Api
     * and returns it if existent, otherwise null.
     *
     * @param string $doi DOI of this entity
     * @return Work|null Work instance or null
     */
    protected static function readWorkFromApiByDoi($doi)
    {
        /** @var CrossRefApi $api */
        $api = AbstractApi::instance('crossref');
        $workData = $api->getDoiInformation($doi);

        if ($workData === null) {
            return null;
        }

        $work = new Work(
            null,
            $workData['title'][0],
            isset($workData['subtitle'][0]) ? $workData['subtitle'][0] : '',
            $workData['created']['date-parts'][0][0],
            strtolower($workData['DOI'])
        );

        // Create authors.
        if (isset($workData['author']) && is_array($workData['author']) && count($workData['author']) > 0 ) {
            foreach ($workData['author'] as $authorData) {
                if (!$author = Author::readByFirstLastName(
                    $authorData['given'],
                    $authorData['family']
                )) {
                    $author = new Author(
                        null,
                        $authorData['given'],
                        $authorData['family']
                    );
                    $author->create();
                }

                $work->authors[(string)$author->getId()] = $author;
                $work->authorIds[] = $author->getId();
            }
        }

        // Create journal.
        if (isset($workData['type']) && $workData['type'] == 'journal-article') {
            if (!$journal = Journal::readByIssn($workData['ISSN'][0])) {
                $journal = new Journal(
                    null,
                    $workData['short-container-title'][0],
                    $workData['ISSN'][0]
                );
                $journal->create();
            }

            $work->journals[(string)$journal->getId()] = $journal;
            $work->journalIds[] = $journal->getId();
        }

        // Create references.
        if (isset($workData['reference']) && count($workData['reference']) > 0) {
            foreach ($workData['reference'] as $referenceData) {
                if (isset($referenceData['DOI'])) {
                    $work->workDois[] = strtolower($referenceData['DOI']);
                }
            }
        }

        $work->create();
        return $work;
    }

    /**
     * Sets the authors and journals by given author/journal IDs.
     */
    protected function setAuthorsJournals()
    {
        if (count($this->journalIds) != count($this->journals)) {
            foreach ($this->journalIds as $journalId) {
                $journal = Journal::read($journalId);
                $this->journals[(string)$journal->getId()] = $journal;
            }
        }

        if (count($this->authorIds) != count($this->authors)) {
            foreach ($this->authorIds as $authorId) {
                $author = Author::read($authorId);
                $this->authors[(string)$author->getId()] = $author;
            }
        }
    }

    /**
     * Read a work entity and returns it if existent, otherwise null.
     *
     * @param string $doi DOI of this entity
     * @param bool $fromApi if true,
     * @return Work|null Work instance or null
     */
    public static function readByDoi($doi, $fromApi = true)
    {
        $sql = 'SELECT w.id_work, w.title, w.subtitle, w.work_year, w.doi,
                  (SELECT GROUP_CONCAT(wj.id_journal) FROM work_journal wj WHERE wj.id_work = w.id_work)
                  AS journal_ids,
                  (SELECT GROUP_CONCAT(wa.id_author) FROM work_author wa WHERE wa.id_work = w.id_work)
                  AS author_ids,
                  (SELECT GROUP_CONCAT(LOWER(q.doi_work_quoted)) FROM quote q WHERE q.doi_work = w.doi)
                  AS work_dois
                FROM work w WHERE doi = ?';
        $sqlParams = array($doi);

        // Fetch result from the database.
        $sqlResult = Database::instance()->select($sql, $sqlParams);

        // If we do not have exactly one result, try to fetch information from API.
        if (count($sqlResult) != 1) {
            // If
            if (!$fromApi) {
                return null;
            }
            return static::readWorkFromApiByDoi($doi);
        }

        $work = new Work(
            DataTypeHelper::instance()->get($sqlResult[0]['id_work'], 'int'),
            $sqlResult[0]['title'],
            $sqlResult[0]['subtitle'],
            DataTypeHelper::instance()->get($sqlResult[0]['work_year'], 'int'),
            strtolower($sqlResult[0]['doi']),
            DataTypeHelper::instance()->getArray(explode(',', $sqlResult[0]['author_ids']), 'int'),
            DataTypeHelper::instance()->getArray(explode(',', $sqlResult[0]['journal_ids']), 'int'),
            DataTypeHelper::instance()->getArray(explode(',', $sqlResult[0]['work_dois']), 'string')
        );
        static::addToCache($work);
        $work->setAuthorsJournals();

        return $work;
    }

    /**
     * Adds an author ID to the authors.
     *
     * @param int $authorId author identifier
     */
    public function addAuthorId($authorId)
    {
        if (in_array($authorId, $this->authorIds)) {
            return;
        }

        if ($author = Author::read($authorId)) {
            $this->authorIds[] = $authorId;
            $this->authors[(string)$author->getId()] = $author;
        }
    }

    /**
     * Adds an journal ID to the journals.
     *
     * @param int $journalId journal identifier
     */
    public function addJournalId($journalId)
    {
        if (in_array($journalId, $this->journalIds)) {
            return;
        }

        if ($journal = Journal::read($journalId)) {
            $this->journalIds[] = $journalId;
            $this->journals[(string)$journal->getId()] = $journal;
        }
    }

    /**
     * Adds a DOI to this work entity.
     *
     * @param string $doiReference DOI reference to add
     * @return bool true if successful
     */
    public function addDoiReference($doiReference)
    {
        // If the DOI reference is already given, abort.
        if (in_array($doiReference, $this->workDois)) {
            return false;
        }

        $this->insertDoiReference($doiReference);
        $this->workDois[] = $doiReference;

        return true;
    }

    /**
     * Removes a DOI from this work entity.
     *
     * @param string $doiReference DOI reference to remove
     */
    public function removeDoiReference($doiReference)
    {
        // If the DOI reference is not given, abort.
        if (
            (string)$this->getDoi() == ''
            || (string)$doiReference == ''
            || !in_array($doiReference, $this->workDois)
        ) {
            return;
        }

        $sql = 'DELETE FROM quote WHERE doi_work = ? AND doi_work_quoted = ?';
        $sqlParams = array(strtolower($this->getDoi()), strtolower($doiReference));
        Database::instance()->updateOrDelete($sql, $sqlParams);

        unset($this->workDois[$doiReference]);
    }

    /**
     * Adds an author to this work entity.
     *
     * @param Author $author author entity to add
     * @return bool true if successful
     */
    public function addAuthor(Author $author)
    {
        // If the author is already given, abort.
        if (in_array($author->getId(), $this->authorIds)) {
            return false;
        }

        $sql = 'INSERT INTO work_author (id_work, id_author) VALUES (?, ?)';
        $sqlParams = array($this->id, $author->getId());
        Database::instance()->insert($sql, $sqlParams);
        $this->authorIds[] = $author->getId();
        $this->authors[(string)$author->getId()] = $author;

        return true;
    }

    /**
     * Removes an author from this work entity.
     *
     * @param Author $author author entity
     */
    public function removeAuthor(Author $author)
    {
        $sql = 'DELETE FROM work_author WHERE id_work = ? AND id_author = ?';
        $sqlParams = array($this->getId(), $author->getId());
        Database::instance()->updateOrDelete($sql, $sqlParams);

        unset($this->authorIds[(string)$author->getId()]);
        unset($this->authors[(string)$author->getId()]);
    }

    /**
     * Adds a journal to this work entity.
     *
     * @param Journal $journal journal entity to add
     * @return bool true if successful
     */
    public function addJournal(Journal $journal)
    {
        // If the author is already given, abort.
        if (in_array($journal->getId(), $this->journalIds)) {
            return false;
        }

        $sql = 'INSERT INTO work_journal (id_work, id_journal) VALUES (?, ?)';
        $sqlParams = array($this->id, $journal->getId());
        Database::instance()->insert($sql, $sqlParams);
        $this->journalIds[] = $journal->getId();
        $this->journals[(string)$journal->getId()] = $journal;

        return true;
    }

    /**
     * Removes a journal from this work entity.
     *
     * @param Journal $journal journal entity
     */
    public function removeJournal(Journal $journal)
    {
        $sql = 'DELETE FROM work_journal WHERE id_work = ? AND id_journal = ?';
        $sqlParams = array($this->getId(), $journal->getId());
        Database::instance()->updateOrDelete($sql, $sqlParams);

        unset($this->journalIds[(string)$journal->getId()]);
        unset($this->journals[(string)$journal->getId()]);
    }

    /**
     * Returns a list of DOIs, that quote this work or null.
     *
     * @return array|null list of DOIs
     */
    public function getDoisQuote()
    {
        if ($this->doi == '') {
            return null;
        }

        $sql = 'SELECT doi_work FROM quote WHERE doi_work_quoted = ?';
        $sqlParams = array($this->doi);

        // Fetch result from the database.
        $sqlResult = Database::instance()->select($sql, $sqlParams);

        // If we have no result, return null.
        if (count($sqlResult) == 0) {
            return null;
        }

        $dois = array();
        foreach ($sqlResult as $doi) {
            $dois[] = $doi['doi_work'];
        }

        return $dois;
    }
}
