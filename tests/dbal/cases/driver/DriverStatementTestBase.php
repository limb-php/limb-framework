<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver;

use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbTypeInfo;
use limb\dbal\src\exception\lmbDbException;
use PHPUnit\Framework\TestCase;

abstract class DriverStatementTestBase extends TestCase
{
    /** @var $connection lmbDbConnectionInterface */
    protected $connection;

    function setTypedValue($type, $column, $value)
    {
        $setterList = (new lmbDbTypeInfo)->getColumnTypeAccessors();
        $setter = $setterList[$type];
        $this->assertNotNull($setter);

        $sql = "
          INSERT INTO standard_types (
              $column
          ) VALUES (
              :$column:
          )";
        $stmt = $this->connection->newStatement($sql);

        $stmt->$setter($column, $value);

        $id = $stmt->insertId('id');

        $sql = "SELECT * FROM standard_types WHERE id = :id:";
        $stmt = $this->connection->newStatement($sql);
        $stmt->setInteger('id', $id);
        $record = $stmt->getOneRecord();

        return $record;
    }

    function testSetNull()
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setNull('literal');
        $this->assertEquals($stmt->getOneValue(), null);

        $sql = "
          INSERT INTO standard_types (
              type_smallint,
              type_integer,
              type_boolean,
              type_char,
              type_varchar,
              type_clob,
              type_float,
              type_double,
              type_decimal,
              type_timestamp,
              type_date,
              type_time,
              type_blob
          ) VALUES (
              :type_smallint:,
              :type_integer:,
              :type_boolean:,
              :type_char:,
              :type_varchar:,
              :type_clob:,
              :type_float:,
              :type_double:,
              :type_decimal:,
              :type_timestamp:,
              :type_date:,
              :type_time:,
              :type_blob:
          )";
        $stmt = $this->connection->newStatement($sql);

        $stmt->setNull('type_smallint');
        $stmt->setNull('type_integer');
        $stmt->setNull('type_boolean');
        $stmt->setNull('type_char');
        $stmt->setNull('type_varchar');
        $stmt->setNull('type_clob');
        $stmt->setNull('type_float');
        $stmt->setNull('type_double');
        $stmt->setNull('type_decimal');
        $stmt->setNull('type_timestamp');
        $stmt->setNull('type_date');
        $stmt->setNull('type_time');
        $stmt->setNull('type_blob');

        $id = $stmt->insertId('id');

        $sql = "SELECT * FROM standard_types WHERE id = :id:";
        $stmt = $this->connection->newStatement($sql);
        $stmt->setInteger('id', $id);
        $record = $stmt->getOneRecord();

        /* generic gets */
        $this->assertNull($record->get('type_smallint'));
        $this->assertNull($record->get('type_integer'));
        $this->assertNull($record->get('type_boolean'));
        $this->assertNull($record->get('type_char'));
        $this->assertNull($record->get('type_varchar'));
        $this->assertNull($record->get('type_clob'));
        $this->assertNull($record->get('type_float'));
        $this->assertNull($record->get('type_double'));
        $this->assertNull($record->get('type_decimal'));
        $this->assertNull($record->get('type_timestamp'));
        $this->assertNull($record->get('type_date'));
        $this->assertNull($record->get('type_time'));
        $this->assertNull($record->get('type_blob'));

        /* typed gets */
        $this->assertNull($record->getInteger('type_smallint'));
        $this->assertNull($record->getInteger('type_integer'));
        $this->assertNull($record->getBoolean('type_boolean'));
        $this->assertNull($record->getString('type_char'));
        $this->assertNull($record->getString('type_varchar'));
        $this->assertNull($record->getString('type_clob'));
        $this->assertNull($record->getFloat('type_float'));
        $this->assertNull($record->getStringFixed('type_double'));
        $this->assertNull($record->getStringFixed('type_decimal'));
        $this->assertNull($record->getStringTimeStamp('type_timestamp'));
        $this->assertNull($record->getStringDate('type_date'));
        $this->assertNull($record->getStringTime('type_time'));
        $this->assertNull($record->getString('type_blob'));
    }

    function testSetSmallInt()
    {
        $this->_checkSmallIntValue(1);
        $this->_checkSmallIntValue(0);
        $this->_checkSmallIntValue(null);
        $this->_checkSmallIntValue(32767);
        $this->_checkSmallIntValue(-32767);
        try {
            $this->_checkSmallIntValue('foo');
            $this->fail();
        } catch (lmbDbException $e) {
            $this->assertTrue(true);
        }
    }

    function testSetInteger()
    {
        $this->_checkIntegerValue(1);
        $this->_checkIntegerValue(0);
        $this->_checkIntegerValue(null);
        $this->_checkIntegerValue(99999);
        $this->_checkIntegerValue(-99999);
        try {
            $this->_checkIntegerValue('foo');
            $this->fail();
        } catch (lmbDbException $e) {
            $this->assertTrue(true);
        }
    }

    function testSetBoolean()
    {
        $this->_checkBooleanValue(null);
        $this->_checkBooleanValue(true);
        $this->_checkBooleanValue(false);
        $this->_checkBooleanValue(1);
        $this->_checkBooleanValue(0);
    }

    function testSetBlob()
    {
        $this->_checkBlobValue(null);
        $this->_checkBlobValue('blobvalue');
    }

    function testSetFloat()
    {
        $this->_checkFloatValue((float)0);
        $this->_checkFloatValue(null);
        $this->_checkFloatValue(3.14);
        $this->_checkFloatValue('3.14');
        try {
            $this->_checkFloatValue('foo');
            $this->fail();
        } catch (lmbDbException $e) {
            $this->assertTrue(true);
        }
    }

    function testSetDouble()
    {
        $this->_checkDoubleValue(0);
        $this->_checkDoubleValue((float)0);
        $this->_checkDoubleValue(null);
        $this->_checkDoubleValue(3.14);
        $this->_checkDoubleValue('3.14');
        try {
            $this->_checkDoubleValue('foo');
            $this->fail();
        } catch (lmbDbException $e) {
            $this->assertTrue(true);
        }
    }

    function testSetDecimal()
    {
        $this->_checkDecimalValue(0);
        $this->_checkDecimalValue((float)0);
        $this->_checkDecimalValue(null);
        $this->_checkDecimalValue(3.14);
        $this->_checkDecimalValue('3.14');
        $this->_checkDecimalValue('1234567890123456789.01'); // To big for float
        try {
            $this->_checkDecimalValue('foo');
            $this->fail();
        } catch (lmbDbException $e) {
            $this->assertTrue(true);
        }
    }

    function testSetChar()
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');

        $string_list = array("Hello 'World!'",
            '"', '\'', '\\', '\\"', '\\\'', '\\0', '\\1',
            "%", "_", '&', '<', '>', '$', '`');
        foreach ($string_list as $value) {
            $stmt->setChar('literal', $value);
            $this->assertEquals($stmt->getOneValue(), $value);
        }

        foreach ($string_list as $value) {
            $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_CHAR, 'type_char', $value);
            //some databases fill char fields with spaces and we have to trim values
            $this->assertEquals(trim($record->getString('type_char')), $value);
            $this->assertEquals(trim($record->get('type_char')), $value);
        }

        $value = null;
        $stmt->setChar('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $value = null;
        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_CHAR, 'type_char', $value);
        $this->assertEquals($record->getString('type_char'), $value);
        $this->assertEquals($record->get('type_char'), $value);

        $value = ' trim ';
        $value = null;
        $stmt->setChar('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);
    }

    function testSetVarChar()
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');

        $string_list = array("Hello 'World!'",
            '"', '\'', '\\', '\\"', '\\\'', '\\0', '\\1',
            "%", "_", '&', '<', '>', '$', '`');
        foreach ($string_list as $value) {
            $stmt->setVarChar('literal', $value);
            $this->assertEquals($stmt->getOneValue(), $value);
        }

        foreach ($string_list as $value) {
            $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_VARCHAR, 'type_varchar', $value);
            $this->assertEquals($record->getString('type_varchar'), $value);
            $this->assertEquals($record->get('type_varchar'), $value);
        }

        $value = null;
        $stmt->setVarChar('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $value = null;
        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_VARCHAR, 'type_varchar', $value);
        $this->assertEquals($record->getString('type_varchar'), $value);
        $this->assertEquals($record->get('type_varchar'), $value);

        $value = ' trim ';
        $stmt->setVarChar('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        //$value = ' trim ';
        //$record = $this->setTypedValue(lmbDbTypeInfo::TYPE_VARCHAR, 'type_varchar', $value);
        //$this->assertIdentical($record->getString('type_varchar'), rtrim($value));
        //$this->assertEquals($record->get('type_varchar'), rtrim($value));
    }

    function testSetDate()
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');

        $value = null;
        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
        $this->assertEquals($record->getStringDate('type_date'), $value);
        $this->assertEquals($record->get('type_date'), $value);

        $value = '2009-12-28';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
        $this->assertEquals($record->getStringDate('type_date'), $value);
        $this->assertEquals($record->get('type_date'), $value);

        $value = '1941-12-07';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
        $this->assertEquals($record->getStringDate('type_date'), $value);
        $this->assertEquals($record->get('type_date'), $value);

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
        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
        $this->assertEquals($record->getString('type_time'), $value);
        $this->assertEquals($record->get('type_time'), $value);

        $value = '06:01:01';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
        $this->assertEquals($record->getStringDate('type_time'), $value);
        $this->assertEquals($record->get('type_time'), $value);

        $value = '18:01:01';

        $stmt->setDate('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
        $this->assertEquals($record->getStringDate('type_time'), $value);
        $this->assertEquals($record->get('type_time'), $value);

        $value = 'Bad Time Value';
        // What should the expected behavior be?
    }

    function testSetTimeStamp()
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');

        $value = null;
        $stmt->setTime('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIMESTAMP, 'type_timestamp', $value);
        $this->assertEquals($record->getStringTimeStamp('type_timestamp'), $value);
        $this->assertEquals($record->getIntegerTimeStamp('type_timestamp'), $value);
        $this->assertEquals($record->get('type_timestamp'), $value);

        $value = '2009-12-28 18:01:01';
        $stmt->setTime('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIMESTAMP, 'type_timestamp', $value);
        $this->assertEquals($record->getStringTimeStamp('type_timestamp'), $value);
        $this->assertEquals($record->get('type_timestamp'), $value);

        $value = '2009-12-28 06:01:01';
        $stmt->setTime('literal', $value);
        $this->assertEquals($stmt->getOneValue(), $value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIMESTAMP, 'type_timestamp', $value);
        $this->assertEquals($record->getStringTimeStamp('type_timestamp'), $value);
        $this->assertEquals($record->getIntegerTimeStamp('type_timestamp'),
            mktime(6, 1, 1, 12, 28, 2009));
        $this->assertEquals($record->get('type_timestamp'), $value);

        $value = 'Bad TimeStamp Value';
        // What should the expected behavior be?
    }

    protected function _checkSmallIntValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setSmallInt('literal', $exp_value);
        $this->assertEquals($exp_value, $stmt->getOneValue());

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_SMALLINT, 'type_smallint', $value);
        $this->assertEquals($exp_value, $record->getInteger('type_smallint'));
        $this->assertEquals($exp_value, $record->get('type_smallint'));
    }

    protected function _checkIntegerValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setInteger('literal', $exp_value);
        $this->assertEquals($exp_value, $stmt->getOneValue());

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_INTEGER, 'type_integer', $exp_value);
        $this->assertEquals($exp_value, $record->getInteger('type_integer'));
        $this->assertEquals($exp_value, $record->get('type_integer'));
    }

    protected function _checkBooleanValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setBoolean('literal', $exp_value);
        $this->assertEquals($exp_value, $stmt->getOneValue());

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BOOLEAN, 'type_boolean', $value);
        if (is_null($exp_value)) {
            $this->assertNull($record->getBoolean('type_boolean'));
        } else {
            $this->assertEquals((boolean)$exp_value, $record->getBoolean('type_boolean'));
        }
    }

    protected function _checkBlobValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setBlob('literal', $exp_value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BLOB, 'type_blob', $exp_value);
        if (is_null($exp_value)) {
            $this->assertNull($record->getBlob('type_blob'));
        } else {
            $this->assertEquals($exp_value, $record->getBlob('type_blob'));
        }
    }

    protected function _checkDecimalValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setDecimal('literal', $exp_value);
        $this->assertEquals($exp_value, $stmt->getOneValue());

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DECIMAL, 'type_decimal', $exp_value);
        $this->assertEquals($exp_value, $record->getStringFixed('type_decimal'));
        $this->assertEquals($exp_value, $record->get('type_decimal'));
    }

    protected function _checkDoubleValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setDouble('literal', $exp_value);
        $this->assertEquals($exp_value, $stmt->getOneValue());

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DOUBLE, 'type_double', $exp_value);
        if (is_string($exp_value)) {
            $this->assertEquals($exp_value, $record->getStringFixed('type_double'));
        } else {
            $this->assertEquals($exp_value, $record->getFloat('type_double'));
        }
        $this->assertEquals($exp_value, $record->get('type_double'));
    }

    protected function _checkFloatValue($exp_value)
    {
        $stmt = $this->connection->newStatement('SELECT :literal:');
        $stmt->setFloat('literal', $exp_value);
        $this->assertEquals($stmt->getOneValue(), (float)$exp_value);

        $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_FLOAT, 'type_float', $exp_value);
        if (is_null($exp_value)) {
            $this->assertNull($record->getFloat('type_float'));
        } else {
            $this->assertEquals((float)$exp_value, $record->getFloat('type_float'));
        }
        $this->assertEquals($exp_value, $record->get('type_float'));
    }

}
