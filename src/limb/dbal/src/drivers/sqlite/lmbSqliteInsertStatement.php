<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\sqlite;

use limb\dbal\src\drivers\lmbDbInsertStatementInterface;

/**
 * class lmbSqliteInsertStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteInsertStatement extends lmbSqliteManipulationStatement implements lmbDbInsertStatementInterface
{
    function insertId($field_name = 'id')
    {
        $this->execute();

        if (isset($this->parameters[$field_name]) && !empty($this->parameters[$field_name]))
            return $this->parameters[$field_name];
        else
            return $this->connection->getSequenceValue();
    }
}
