<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlRecord;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverRecordSetTestBase;

require_once(dirname(__FILE__) . '/../../.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlRecordSetTest extends DriverRecordSetTestBase
{

  function setUp(): void
  {
      parent::init(lmbPgsqlRecord::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());

    parent::setUp();
  }

    function testCount2()
    {
        $sql = "SELECT *, (extract(epoch from now())::int - type_integer) AS new_column FROM standard_types";
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->paginate(0, 2);

        $this->assertEquals($rs->count(), 3);
        $this->assertEquals($rs->countPaginated(), 2);
    }
}
