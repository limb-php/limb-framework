<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteRecord;
use tests\dbal\cases\driver\DriverRecordTestBase;
use limb\toolkit\src\lmbToolkit;

require_once('.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteRecordTest extends DriverRecordTestBase
{

  function setUp(): void
  {
      parent::init(lmbSqliteRecord::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverSqliteSetup($this->connection);

    parent::setUp();
  }
}
