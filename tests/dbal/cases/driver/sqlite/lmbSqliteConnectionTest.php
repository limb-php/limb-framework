<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteInsertStatement;
use limb\dbal\src\drivers\sqlite\lmbSqliteManipulationStatement;
use limb\dbal\src\drivers\sqlite\lmbSqliteQueryStatement;
use limb\dbal\src\drivers\sqlite\lmbSqliteStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbSqliteConnectionTest extends DriverConnectionTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $this->init(
            lmbSqliteQueryStatement::class,
            lmbSqliteInsertStatement::class,
            lmbSqliteManipulationStatement::class,
            lmbSqliteStatement::class
        );

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markTestSkipped("Wrong connection to SQLITE");

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }

    function testSocketConnection()
    {
        $this->markTestSkipped('Socket connection is not supported by this driver.');
    }
}
