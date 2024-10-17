<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\mysql;

use limb\dbal\drivers\lmbDbInsertStatementInterface;

/**
 * class lmbMysqlInsertStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqlInsertStatement.php 6243 2007-08-29 11:53:10Z
 */
class lmbMysqlInsertStatement extends lmbMysqlManipulationStatement implements lmbDbInsertStatementInterface
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
