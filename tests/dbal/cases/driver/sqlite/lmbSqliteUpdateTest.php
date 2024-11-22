<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteManipulationStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverUpdateTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbSqliteUpdateTest extends DriverUpdateTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(lmbSqliteManipulationStatement::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markTestSkipped("Wrong connection to SQLITE");

        $this->connection->getConnection()->busyTimeout(250);

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }
}
