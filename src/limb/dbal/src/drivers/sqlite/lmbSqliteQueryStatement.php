<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\sqlite;

use limb\dbal\src\drivers\lmbDbQueryStatementInterface;

/**
 * class lmbSqliteQueryStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteQueryStatement extends lmbSqliteStatement implements lmbDbQueryStatementInterface
{
    function getOneRecord()
    {
        $record = new lmbSqliteRecord();
        $rset = $this->connection->execute($this->getSQL());
        $values = $rset->fetchArray(SQLITE3_ASSOC);
        if (is_array($values)) {
            $record->import($values);
            return $record;
        }
    }

    function getOneValue()
    {
        $rset = $this->connection->execute($this->getSQL());
        $values = $rset->fetchArray(SQLITE3_ASSOC);
        return current($values);
    }

    function getOneColumnAsArray()
    {
        $column = array();
        $rset = $this->connection->execute($this->getSQL());
        while ($value = $rset->fetchArray(SQLITE3_ASSOC))
            $column[] = current($value);
        return $column;
    }

    function getRecordSet(): lmbSqliteRecordSet
    {
        return new lmbSqliteRecordSet($this->connection, $this->getSQL());
    }
}
