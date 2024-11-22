<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mysql;

use limb\dbal\src\drivers\mysql\lmbMysqlManipulationStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverUpdateTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMysqlUpdateTest extends DriverUpdateTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(lmbMysqlManipulationStatement::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMysqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
