<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\mssql;

use limb\dbal\src\drivers\mssql\lmbMssqlInsertStatement;
use limb\dbal\src\drivers\mssql\lmbMssqlManipulationStatement;
use limb\dbal\src\drivers\mssql\lmbMssqlQueryStatement;
use limb\dbal\src\drivers\mssql\lmbMssqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlConnectionTest extends DriverConnectionTestBase
{

  function lmbMssqlConnectionTest()
  {
    parent :: DriverConnectionTestBase(
        lmbMssqlQueryStatement::class,
        lmbMssqlInsertStatement::class,
        lmbMssqlManipulationStatement::class,
        lmbMssqlStatement::class);
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMssqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}
