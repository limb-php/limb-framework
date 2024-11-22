<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\sqlite;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverDeleteTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbSqliteDeleteTest extends DriverDeleteTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markTestSkipped("Wrong connection to SQLITE");

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }
}
