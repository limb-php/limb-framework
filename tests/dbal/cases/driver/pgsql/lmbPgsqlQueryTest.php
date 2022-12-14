<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\pgsql;

use tests\dbal\cases\driver\DriverQueryTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlQueryTest extends DriverQueryTestBase
{

  function lmbPgsqlQueryTest()
  {
    parent :: DriverQueryTestBase('lmbPgsqlRecord');
  }

  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    //DriverPgsqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}
