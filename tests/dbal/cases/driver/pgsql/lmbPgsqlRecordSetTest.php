<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlRecord;
use limb\dbal\src\drivers\pgsql\lmbPgsqlRecordSet;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverRecordSetTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbPgsqlRecordSetTest extends DriverRecordSetTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(lmbPgsqlRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverPgsqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }

    function testCount2()
    {
        //$sql = "SELECT *, (extract(epoch from now())::int - btime) AS new_column_time FROM founding_fathers";
        $sql = "SELECT *, (" . time() . " - btime) AS new_column_time FROM founding_fathers";
        $exp_sql = "SELECT COUNT(*) FROM founding_fathers";
        /** @var lmbPgsqlRecordSet $rs */
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->paginate(0, 2);

        //$this->assertEquals($exp_sql, $countSQL, "Real query is " . $countSQL);
        $this->assertEquals(3, $rs->count());
        $this->assertEquals(2, $rs->countPaginated());
    }
}
