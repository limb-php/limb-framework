<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\mysql;

use limb\dbal\src\drivers\mysql\lmbMysqlRecord;
use limb\dbal\src\drivers\mysql\lmbMysqlStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbMysqlTypeInfoTest extends DriverTypeInfoTestBase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/.setup.php');
    }

    function setUp(): void
    {
        parent::init(lmbMysqlStatement::class, lmbMysqlRecord::class);

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        $this->typeInfo = $this->connection->getTypeInfo();

        parent::setUp();
    }
}
