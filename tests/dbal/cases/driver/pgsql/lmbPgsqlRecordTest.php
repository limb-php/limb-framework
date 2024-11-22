<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlRecord;
use tests\dbal\cases\driver\DriverRecordTestBase;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbPgsqlRecordTest extends DriverRecordTestBase
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
}
