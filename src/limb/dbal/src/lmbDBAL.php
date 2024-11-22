<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src;

use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbRecordInterface;
use limb\dbal\src\drivers\lmbDbRecordSetInterface;
use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\dbal\src\query\lmbSelectQuery;
use limb\dbal\src\query\lmbUpdateQuery;
use limb\dbal\src\query\lmbDeleteQuery;
use limb\dbal\src\query\lmbInsertQuery;
use limb\dbal\src\query\lmbBulkInsertQuery;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\drivers\lmbDbQueryStatementInterface;
use limb\dbal\src\exception\lmbDbException;
use limb\dbal\src\query\lmbInsertOnDuplicateUpdateQuery;

/**
 * class lmbDBAL.
 *
 * @package dbal
 * @version $Id: lmbDBAL.php 8187 2010-04-28 17:48:33Z
 */
class lmbDBAL
{
    /**
     * @param lmbDbDSN $dsn
     */
    static function setDefaultDSN($dsn)
    {
        lmbToolkit::instance()->setDefaultDbDSN($dsn);
    }

    static function setEnvironment($env)
    {
        lmbToolkit::instance()->setDbEnvironment($env);
    }

    /**
     * @param lmbDbDSN $dsn
     * @return lmbDbConnectionInterface
     */
    static function newConnection($dsn)
    {
        return lmbToolkit::instance()->createDbConnection($dsn);
    }

    /**
     * @return lmbDbConnectionInterface
     */
    static function defaultConnection(): lmbDbConnectionInterface
    {
        return lmbToolkit::instance()->getDefaultDbConnection();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDbStatementInterface
     */
    static function newStatement($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        return $conn->newStatement($sql);
    }

    /**
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbSimpleDb
     */
    static function db($conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();

        return new lmbSimpleDb($conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbTableGateway
     */
    static function table($table, $conn = null): lmbTableGateway
    {
        return lmbToolkit::instance()->createTableGateway($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbSelectQuery
     */
    static function selectQuery($table, $conn = null): lmbSelectQuery
    {
        return new lmbSelectQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param string $primary_key_name
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbInsertQuery
     */
    static function insertQuery($table, $primary_key_name, $conn = null): lmbInsertQuery
    {
        return new lmbInsertQuery($table, $primary_key_name, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbInsertOnDuplicateUpdateQuery
     */
    static function insertOnDuplicateUpdateQuery($table, $conn = null)
    {
        return new lmbInsertOnDuplicateUpdateQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbBulkInsertQuery
     */
    static function bulkInsertQuery($table, $conn = null)
    {
        return new lmbBulkInsertQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbUpdateQuery
     */
    static function updateQuery($table, $conn = null): lmbUpdateQuery
    {
        return new lmbUpdateQuery($table, $conn);
    }

    /**
     * @param string $table
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDeleteQuery
     */
    static function deleteQuery($table, $conn = null): lmbDeleteQuery
    {
        return new lmbDeleteQuery($table, $conn);
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDbRecordSetInterface
     */
    static function fetch($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        $stmt = $conn->newStatement($sql);
        if (!$stmt instanceof lmbDbQueryStatementInterface)
            throw new lmbDbException("The result of this SQL query can not be fetched.", array('query' => $sql));
        return $stmt->getRecordSet();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return lmbDbRecordInterface
     */
    static function fetchOneRow($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        $stmt = $conn->newStatement($sql);
        return $stmt->getOneRecord();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     * @return string
     */
    static function fetchOneValue($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        return $conn->newStatement($sql)->getOneValue();
    }

    /**
     * @param string $sql
     * @param lmbDbConnectionInterface|null $conn
     */
    static function execute($sql, $conn = null)
    {
        if (!$conn)
            $conn = lmbToolkit::instance()->getDefaultDbConnection();
        $conn->execute($sql, false);
    }
}
