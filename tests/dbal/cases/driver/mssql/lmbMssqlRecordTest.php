<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver\mssql;

use tests\dbal\cases\driver\DriverRecordTestBase;
use limb\dbal\src\drivers\mssql\lmbMssqlRecord;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlRecordTest extends DriverRecordTestBase
{
  function setUp(): void
  {
      parent::init(lmbMssqlRecord::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverMssqlSetup($this->connection->getConnectionId());

    parent::setUp();
  }
}
