<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal;

use limb\dbal\drivers\lmbDbConnectionInterface;
use limb\dbal\drivers\lmbDbRecordSetInterface;
use limb\dbal\query\lmbInsertQuery;
use limb\dbal\query\lmbSelectQuery;
use limb\dbal\query\lmbUpdateQuery;
use limb\dbal\query\lmbDeleteQuery;
use limb\dbal\criteria\lmbSQLCriteria;
use limb\dbal\query\lmbInsertOnDuplicateUpdateQuery;

/**
 * class lmbSimpleDb.
 *
 * @package dbal
 * @version $Id: lmbSimpleDb.php 8083 2010-01-22 00:57:23Z
 */
class lmbSimpleDb
{
    protected $conn;
    protected $stmt;

    function __construct(lmbDbConnectionInterface $conn)
    {
        $this->conn = $conn;
    }

    function getConnection()
    {
        return $this->conn;
    }

    function getType()
    {
        return $this->conn->getType();
    }

    function execute($sql)
    {
        $this->conn->execute($sql);
    }

    function query($sql)
    {
        return $this->conn->newStatement($sql)->getRecordSet();
    }

    function select($table, $criteria = null, $order = array()): lmbDbRecordSetInterface
    {
        $query = new lmbSelectQuery($table, $this->conn);

        if ($criteria)
            $query->addCriteria(lmbSQLCriteria::objectify($criteria));

        $rs = $query->getRecordSet();

        if (is_array($order) && sizeof($order))
            $rs->sort($order);

        return $rs;
    }

    function selectRecord($table, $criteria = null, $order = array())
    {
        $rs = $this->select($table, $criteria, $order)->paginate(0, 1);
        $rs->rewind();
        if ($rs->valid())
            return $rs->current();
    }

    /**
     * @deprecated
     */
    function getFirstRecordFrom($table_name, $criteria = null, $order = array())
    {
        return $this->selectRecord($table_name, $criteria, $order);
    }

    function count($table_name, $criteria = null)
    {
        $rs = $this->select($table_name, $criteria);
        return $rs->count();
    }

    function countAffected()
    {
        if ($this->stmt)
            return $this->stmt->getAffectedRowCount();
        else
            return 0;
    }

    function insert($table, $values, $primary_key = 'id')
    {
        $query = new lmbInsertQuery($table, $primary_key, $this->conn);

        foreach ($values as $key => $value)
            $query->addField($key, $value);

        $stmt = $query->getStatement();

        if ($primary_key) {
            if (isset($values[$primary_key])) {
                $stmt->execute();
                return $values[$primary_key];
            } else
                return $stmt->insertId($primary_key);
        } else
            $stmt->execute();
    }

    function update($table, $values, $criteria = null)
    {
        $query = new lmbUpdateQuery($table, $this->conn);

        if ($criteria)
            $query->addCriteria(lmbSQLCriteria::objectify($criteria));

        foreach ($values as $key => $value)
            $query->addField($key, $value);

        $this->stmt = $query->getStatement();
        $this->stmt->execute();
        return $this;
    }

    function insert_on_duplicate_update($table, $values, $criteria = null)
    {
        $query = new lmbInsertOnDuplicateUpdateQuery($table, $this->conn);

        if ($criteria)
            $query->addCriteria(lmbSQLCriteria::objectify($criteria));

        foreach ($values as $key => $value)
            $query->addField($key, $value);

        $this->stmt = $query->getStatement();
        $this->stmt->execute();
        return $this;
    }

    function delete($table, $criteria = null)
    {
        $query = new lmbDeleteQuery($table, $this->conn);

        if ($criteria)
            $query->addCriteria(lmbSQLCriteria::objectify($criteria));

        $this->stmt = $query->getStatement();
        $this->stmt->execute();
        return $this;
    }

    function truncate($table)
    {
        $this->conn->newStatement("TRUNCATE TABLE `$table`")->execute();

        return $this;
    }

    function truncateDb()
    {
        $info = $this->conn->getDatabaseInfo();
        foreach ($info->getTableList() as $table)
            $this->conn->newStatement("DELETE FROM $table")->execute();
        return $this;
    }

    function dropTable($table, $if_exists = true)
    {
        $this->conn->newStatement("DROP TABLE " . ($if_exists ? "IF EXISTS" : "") . "`$table`")->execute();

        return $this;
    }

    function cleanup()
    {
        $info = $this->conn->getDatabaseInfo();
        foreach ($info->getTableList() as $table)
            $this->dropTable($table);
        return $this;
    }

    function disconnect()
    {
        $this->conn->disconnect();
        return $this;
    }

    function begin()
    {
        $this->conn->beginTransaction();
        return $this;
    }

    function commit()
    {
        $this->conn->commitTransaction();
        return $this;
    }

    function rollback()
    {
        $this->conn->rollbackTransaction();
        return $this;
    }

    function quote($id)
    {
        return $this->conn->quoteIdentifier($id);
    }
}
