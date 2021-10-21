<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers\mysqli;

use limb\dbal\src\drivers\mysqli\lmbMysqliManipulationStatement;
use limb\dbal\src\drivers\lmbDbInsertStatementInterface;

/**
 * class lmbMysqliInsertStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqliInsertStatement.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbMysqliInsertStatement extends lmbMysqliManipulationStatement implements lmbDbInsertStatementInterface
{
  function insertId($field_name = 'id')
  {
    $this->execute();

    if(isset($this->parameters[$field_name]) && !empty($this->parameters[$field_name]))
      return $this->parameters[$field_name];
    else
      return $this->connection->getSequenceValue(null, null);
  }
}


