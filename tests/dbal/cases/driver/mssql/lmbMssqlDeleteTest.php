<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mssql;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverDeleteTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMssqlDeleteTest extends DriverDeleteTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        if( !function_exists('sqlsrv_query') )
            $this->markTestSkipped('no driver mssql');

        $this->connection = lmbToolkit:: instance()->getDefaultDbConnection();
        DriverMssqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
