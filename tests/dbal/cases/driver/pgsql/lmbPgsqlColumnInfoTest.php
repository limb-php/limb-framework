<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\pgsql;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverColumnInfoTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlColumnInfoTest extends DriverColumnInfoTestBase
{
  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}
