<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\mysqli;

use limb\dbal\src\drivers\lmbDbManipulationStatementInterface;

/**
 * class lmbMysqliManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqliManipulationStatement.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbMysqliManipulationStatement extends lmbMysqliStatement implements lmbDbManipulationStatementInterface
{
  function getAffectedRowCount()
  {
    return mysqli_affected_rows($this->connection->getConnectionId());
  }
}
