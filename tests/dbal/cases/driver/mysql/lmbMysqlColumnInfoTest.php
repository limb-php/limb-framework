<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mysql;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverColumnInfoTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMysqlColumnInfoTest extends DriverColumnInfoTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMysqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
