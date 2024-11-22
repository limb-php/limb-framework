<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\linter;

use limb\dbal\src\drivers\lmbDbInsertStatementInterface;

/**
 * class lmbLinterInsertStatement.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterInsertStatement extends lmbLinterManipulationStatement implements lmbDbInsertStatementInterface
{
    function insertId($field_name = 'id')
    {
        $this->execute();

        if (isset($this->parameters[$field_name]) && !empty($this->parameters[$field_name]))
            return $this->parameters[$field_name];
        else
            return $this->connection->getSequenceValue();
    }

    function _retriveTableName($sql)
    {
        preg_match('/INSERT\s+INTO\s+(\S+)/i', $sql, $m);
        //removing possible quotes
        $m[1] = str_replace('"', '', $m[1]);
        return $m[1];
    }
}
