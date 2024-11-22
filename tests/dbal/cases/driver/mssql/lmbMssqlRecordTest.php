<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mssql;

use tests\dbal\cases\driver\DriverRecordTestBase;
use limb\dbal\src\drivers\mssql\lmbMssqlRecord;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMssqlRecordTest extends DriverRecordTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        if( !function_exists('sqlsrv_query') )
            $this->markTestSkipped('no driver mssql');

        parent::init(lmbMssqlRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMssqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
