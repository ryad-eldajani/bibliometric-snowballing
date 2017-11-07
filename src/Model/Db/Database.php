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

namespace BS\Model\Db;


use BS\Model\App;
use BS\Model\Exceptions\DbException;

class Database
{
    // Connection types
    const CONNECTION_BS = 'bs';
    const CONNECTION_TYPO3 = 'typo3';

    // Parameter types for mysqli->bind_param()
    const PARAM_STRING = 's';
    const PARAM_INTEGER = 'i';
    const PARAM_DOUBLE = 'd';
    const PARAM_BLOB = 'b';

    /**
     * @var Database|null instance
     */
    protected static $instance = null;
    /**
     * @var \mysqli[] $connections
     */
    protected $connections = array();

    /**
         * Database constructor.
         */
    private function __construct()
    {
        // Instantiate BS connection.
        $this->connections[self::CONNECTION_BS] = new \mysqli(
            App::instance()->getConfig('db/hostname'),
            App::instance()->getConfig('db/username'),
            App::instance()->getConfig('db/password'),
            App::instance()->getConfig('db/database'),
            App::instance()->getConfig('db/port')
        );

        // Instantiate Typo3 connection, if TYPO3_CONF_VARS are set in $GLOBALS.
        if (isset($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'])) {
            $this->connections[self::CONNECTION_TYPO3] = new \mysqli(
                $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'],
                $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'],
                $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'],
                $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'],
                $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port']
            );
        }

        // Check each connection for errors, throw DbException if error appeared.
        foreach ($this->connections as $connection) {
            if ($connection->connect_errno) {
                throw new DbException(
                    'Failed to connect to MySQL: (' . $connection->connect_errno
                    . ') ' . $connection->connect_error
                );
            }
        }
    }

    /**
     * Returns the singleton.
     * @return Database instance
     */
    public static function instance()
    {
        if (!isset(Database::$instance)) {
            Database::$instance = new Database();
        }

        return Database::$instance;
    }

    /**
     * Returns a MySQLi statement instance with prepared statement.
     * Binds parameters to the prepared statement if necessary.
     * Unfortunately \mysqli_stmt->bind_param() does not support arrays as
     * parameters, therefore we have to setup a reference array and call
     * bind_param() via call_user_func_array.
     * See: http://www.pontikis.net/blog/dynamically-bind_param-array-mysqli
     *
     * @param \mysqli $connection MySQLi connection
     * @param string $sql SQL query
     * @param array $parameters parameters to bind
     * @return \mysqli_stmt MySQLi statement instance
     * @throws DbException
     */
    protected function getStatement(\mysqli $connection, $sql, $parameters = array())
    {
        // Prepare SQL statement.
        if (!$statement = $connection->prepare($sql)) {
            throw new DbException('Prepare failed: (' . $connection->errno
                . ') ' . $connection->error);
        }

        // If we have no parameters, return $statement instance only.
        if (count($parameters) == 0) {
            return $statement;
        }

        // We have parameters to bind to the statement.
        // Build up $parameterTypes (e.g. "ssi" for 2x string and 1x integer).
        $parameterTypes = '';
        foreach ($parameters as $parameter) {
            $parameterType = self::PARAM_STRING;
            if (is_int($parameter)) {
                $parameterType = self::PARAM_INTEGER;
            } elseif (is_double($parameter)) {
                $parameterType = self::PARAM_DOUBLE;
            } elseif (is_file($parameter)) {
                $parameterType = self::PARAM_BLOB;
            }

            $parameterTypes .= $parameterType;
        }

        // Build $bindParameters array (first element has to be the parameter
        // types, rest the parameter values).
        $bindParameters[] = & $parameterTypes;
        for ($i = 0; $i < count($parameters); $i++) {
            $bindParameters[] = & $connection->real_escape_string($parameters[$i]);
        }

        // Call $statement->bind_param() with $bindParameters.
        call_user_func_array(array($statement, 'bind_param'), $bindParameters);

        return $statement;
    }

    /**
     * Executes a SELECT statement and returns the result as array for each
     * result row.
     *
     * @param string $sql SELECT statement
     * @param array $parameters Parameters to bind
     * @param string $connectionType Connection type (e.g. self::CONNECTION_BS)
     * @return array SQL result
     * @throws DbException
     */
    public function select($sql, $parameters = array(), $connectionType = self::CONNECTION_BS)
    {
        $statement = $this->getStatement($this->connections[$connectionType], $sql, $parameters);
        $statement->execute();
        $statementResult = $statement->get_result();

        // Put result into $resultArray for each result row.
        $resultArray = array();
        while ($resultRow = $statementResult->fetch_array(MYSQLI_ASSOC)) {
            $resultArray[] = $resultRow;
        }

        return $resultArray;
    }
}
