<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\pgsql;

use limb\dbal\src\drivers\pgsql\lmbPgsqlInsertStatement;
use limb\dbal\src\drivers\pgsql\lmbPgsqlManipulationStatement;
use limb\dbal\src\drivers\pgsql\lmbPgsqlQueryStatement;
use limb\dbal\src\drivers\pgsql\lmbPgsqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverConnectionTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbPgsqlConnectionTest extends DriverConnectionTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(
            lmbPgsqlQueryStatement::class,
            lmbPgsqlInsertStatement::class,
            lmbPgsqlManipulationStatement::class,
            lmbPgsqlStatement::class
        );

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverPgsqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }
}
