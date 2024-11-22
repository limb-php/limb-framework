<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mysql;

use limb\dbal\src\drivers\mysql\lmbMysqlRecord;
use tests\dbal\cases\driver\DriverQueryTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMysqlQueryTest extends DriverQueryTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(lmbMysqlRecord::class);

        $this->connection = lmbToolkit:: instance()->getDefaultDbConnection();
        DriverMysqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
