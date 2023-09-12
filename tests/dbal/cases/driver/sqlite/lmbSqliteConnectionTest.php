<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteInsertStatement;
use limb\dbal\src\drivers\sqlite\lmbSqliteManipulationStatement;
use limb\dbal\src\drivers\sqlite\lmbSqliteQueryStatement;
use limb\dbal\src\drivers\sqlite\lmbSqliteStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once('.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteConnectionTest extends DriverConnectionTestBase
{

    function setUp(): void
    {
        $this->init(
            lmbSqliteQueryStatement::class,
            lmbSqliteInsertStatement::class,
            lmbSqliteManipulationStatement::class,
            lmbSqliteStatement::class
        );

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverSqliteSetup($this->connection);

        parent::setUp();
    }

    function testSocketConnection()
    {
        $this->markTestSkipped('Socket connection is not supported by this driver.');
    }
}
