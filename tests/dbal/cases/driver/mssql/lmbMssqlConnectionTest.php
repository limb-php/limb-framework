<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mssql;

use limb\dbal\src\drivers\mssql\lmbMssqlInsertStatement;
use limb\dbal\src\drivers\mssql\lmbMssqlManipulationStatement;
use limb\dbal\src\drivers\mssql\lmbMssqlQueryStatement;
use limb\dbal\src\drivers\mssql\lmbMssqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMssqlConnectionTest extends DriverConnectionTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        if( !function_exists('sqlsrv_query') )
            $this->markTestSkipped('no driver mssql');

        parent::init(
            lmbMssqlQueryStatement::class,
            lmbMssqlInsertStatement::class,
            lmbMssqlManipulationStatement::class,
            lmbMssqlStatement::class);

        $this->connection = lmbToolkit:: instance()->getDefaultDbConnection();
        DriverMssqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
