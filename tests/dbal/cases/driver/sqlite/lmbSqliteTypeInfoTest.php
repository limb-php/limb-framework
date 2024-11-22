<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\sqlite\lmbSqliteRecord;
use limb\dbal\src\drivers\sqlite\lmbSqliteStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbSqliteTypeInfoTest extends DriverTypeInfoTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(
            lmbSqliteStatement::class,
            lmbSqliteRecord::class
        );

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        if($this->connection->getType() != 'sqlite')
            $this->markTestSkipped("Wrong connection to SQLITE");

        DriverSqliteSetup($this->connection);

        $this->typeInfo = $this->connection->getTypeInfo();

        parent::setUp();
    }
}
