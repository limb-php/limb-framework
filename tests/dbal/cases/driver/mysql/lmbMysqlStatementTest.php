<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\mysql;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverStatementTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqlStatementTest extends DriverStatementTestBase
{
  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    //DriverMysqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}
