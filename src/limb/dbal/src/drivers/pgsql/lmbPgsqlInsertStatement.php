<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\pgsql;

use limb\dbal\src\drivers\lmbDbInsertStatementInterface;
use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbPgsqlInsertStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlInsertStatement.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlInsertStatement extends lmbPgsqlManipulationStatement implements lmbDbInsertStatementInterface
{
    function insertId($field_name = 'id')
    {
        $queryId = $this->execute();

        if (isset($this->parameters[$field_name]) && !empty($this->parameters[$field_name])) {
            return $this->parameters[$field_name];
        } else {
            $table = $this->_retriveTableName($this->getSQL());
            $seq = "{$table}_{$field_name}_seq";

            try {
                return $this->connection->getSequenceValue($seq);
            } catch (lmbDbException $exception) {
            }
        }
    }

    function _retriveTableName($sql)
    {
        preg_match('/INSERT\s+INTO\s+(\S+)/i', $sql, $m);
        //removing possible quotes
        $m[1] = str_replace('"', '', $m[1]);
        return $m[1];
    }
}
