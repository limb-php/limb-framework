<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver\mssql;

use limb\dbal\src\drivers\mssql\lmbMssqlTypeInfo;
use limb\toolkit\src\lmbToolkit;
use Tests\dbal\cases\driver\DriverStatementTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlStatementTest extends DriverStatementTestBase
{
    function setUp(): void
    {
        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverMssqlSetup($this->connection->getConnectionId());

        parent::setUp();
    }

    function checkDoubleValue($value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setDouble('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbMssqlTypeInfo::TYPE_DOUBLE, 'type_double', $value);
        if (is_string($value)) {
            $this->assertEquals(round($record->getStringFixed('type_double'), 2), round($value, 2));
        } else {
            $this->assertEquals(round($record->getFloat('type_double'), 2), round($value, 2));
        }
        $this->assertEquals(round($record->get('type_double'), 2), round($value, 2));
    }

    function testSetDate()
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');

        $value = null;
        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbMssqlTypeInfo::TYPE_DATE, 'type_date', $value);
        $this->assertIdentical($record->getStringDate('type_date'), $value);
        $this->assertEquals($record->get('type_date'), $value);

        $value = '2009-12-28';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbMssqlTypeInfo::TYPE_DATE, 'type_date', $value);
        $this->assertIdentical($record->getStringDate('type_date'), $value);
        $this->assertEquals($record->getDate('type_date'), $value);

        $value = '1941-12-07';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbMssqlTypeInfo::TYPE_DATE, 'type_date', $value);
        $this->assertIdentical($record->getStringDate('type_date'), $value);
        $this->assertEquals($record->getDate('type_date'), $value);

        $value = 'Bad Date Value';
        // What should the expected behavior be?
    }

    function testSetTime()
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');

        $value = null;
        $stmt->setTime('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $value = null;
        $record = $this->setTypedValue(lmbMssqlTypeInfo::TYPE_TIME, 'type_time', $value);
        $this->assertIdentical($record->getStringTime('type_time'), $value);
        $this->assertEquals($record->getTime('type_time'), $value);

        $value = '06:01:01';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbMssqlTypeInfo::TYPE_TIME, 'type_time', $value);
        $this->assertIdentical($record->getStringTime('type_time'), $value);
        $this->assertEquals($record->getTime('type_time'), $value);

        $value = '18:01:01';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbMssqlTypeInfo::TYPE_TIME, 'type_time', $value);
        $this->assertIdentical($record->getStringTime('type_time'), $value);
        $this->assertEquals($record->getTime('type_time'), $value);

        $value = 'Bad Time Value';
        // What should the expected behavior be?
    }
}
