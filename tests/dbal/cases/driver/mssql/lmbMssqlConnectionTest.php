<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\mssql;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlConnectionTest extends DriverConnectionTestBase
{

  function lmbMssqlConnectionTest()
  {
    parent :: DriverConnectionTestBase('lmbMssqlQueryStatement', 'lmbMssqlInsertStatement', 'lmbMssqlManipulationStatement', 'lmbMssqlStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMssqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}


