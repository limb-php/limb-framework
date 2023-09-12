<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver\mssql;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverDatabaseInfoTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlDbInfoTest extends DriverDatabaseInfoTestBase
{
  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMssqlSetup($this->connection->getConnectionId());

    parent::setUp();
  }
}
