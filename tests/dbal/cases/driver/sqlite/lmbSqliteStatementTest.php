<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\sqlite;

use limb\dbal\src\drivers\lmbDbTypeInfo;
use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverStatementTestBase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteStatementTest extends DriverStatementTestBase
{
    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
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
