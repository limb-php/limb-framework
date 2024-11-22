<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlManipulationStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverUpdateTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbPgsqlUpdateTest extends DriverUpdateTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(lmbPgsqlManipulationStatement::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverPgsqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
