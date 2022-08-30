<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers\mssql;

use limb\dbal\src\drivers\lmbDbManipulationStatementInterface;

/**
 * class lmbMssqlManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbMssqlManipulationStatement.class.php,v 1.1.1.1 2009/06/08 11:57:21 mike Exp $
 */
class lmbMssqlManipulationStatement extends lmbMssqlStatement implements lmbDbManipulationStatementInterface
{
  function getAffectedRowCount()
  {
    return mssql_rows_affected($this->connection->getConnectionId());
  }
}


