<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\mysql;

use limb\dbal\src\drivers\mysql\lmbMysqlInsertStatement;
use tests\dbal\cases\driver\DriverInsertTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqlInsertTest extends DriverInsertTestBase
{

  function setUp(): void
  {
      parent::init(lmbMysqlInsertStatement::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverMysqlSetup($this->connection->getConnectionId());

    parent::setUp();
  }
}
