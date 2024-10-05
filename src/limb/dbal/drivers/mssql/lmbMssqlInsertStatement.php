<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\mssql;

use limb\dbal\src\drivers\lmbDbInsertStatementInterface;

/**
 * class lmbMssqlInsertStatement.
 *
 * @package dbal
 * @version $Id: lmbMssqlInsertStatement.php,v 1.1.1.1 2009/06/08 11:57:21
 */
class lmbMssqlInsertStatement extends lmbMssqlManipulationStatement implements lmbDbInsertStatementInterface
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
