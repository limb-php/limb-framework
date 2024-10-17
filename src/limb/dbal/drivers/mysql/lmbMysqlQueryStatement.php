<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\mysql;

use limb\dbal\drivers\lmbDbQueryStatementInterface;

/**
 * class lmbMysqlQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqlQueryStatement.php 6243 2007-08-29 11:53:10Z
 */
class lmbMysqlQueryStatement extends lmbMysqlStatement implements lmbDbQueryStatementInterface
{
    function getOneRecord()
    {
        $record = new lmbMysqlRecord();
        $queryId = $this->connection->execute($this->getSQL());
        $values = Mysqli_fetch_assoc($queryId);
        $record->import($values);
        Mysqli_free_result($queryId);
        if (is_array($values))
            return $record;
    }

    function getOneValue()
    {
        $queryId = $this->connection->execute($this->getSQL());
        $row = Mysqli_fetch_row($queryId);
        Mysqli_free_result($queryId);
        if (is_array($row))
            return $row[0];
    }

    function getOneColumnAsArray()
    {
        $column = array();
        $queryId = $this->connection->execute($this->getSQL());
        while (is_array($row = Mysqli_fetch_row($queryId)))
            $column[] = $row[0];
        Mysqli_free_result($queryId);
        return $column;
    }

    function getRecordSet(): lmbMysqlRecordSet
    {
        return new lmbMysqlRecordSet($this->connection, $this->getSQL());
    }
}
