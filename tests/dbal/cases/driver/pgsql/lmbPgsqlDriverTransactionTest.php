<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\pgsql;

use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTransactionTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbPgsqlDriverTransactionTest extends DriverTransactionTestBase
{
    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverPgsqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
