<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\lmbDbTypeInfo;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverStatementTestBase;

require_once(dirname(__FILE__) . '/init.inc.php');

class lmbSqliteStatementTest extends DriverStatementTestBase
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

        $this->connection->getConnection()->busyTimeout(250);

        DriverSqliteSetup($this->connection);

        parent::setUp();
    }

    protected function _checkDecimalValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setDecimal('literal', $exp_value);
        $this->assertEquals($exp_value, (float)$stmt->getOneValue());

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DECIMAL, 'type_decimal', $exp_value);
        $this->assertEquals($exp_value, (float)$record->getStringFixed('type_decimal'));
        $this->assertEquals($exp_value, $record->get('type_decimal'));
    }
}
