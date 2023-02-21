<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\driver\linter;

use limb\dbal\src\drivers\lmbDbTypeInfo;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverStatementTestBase;

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterStatementTest extends DriverStatementTestBase
{
  function setUp(): void
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    parent::setUp();
  }


  function setTypedValue($type, $column, $value)
  {
    $setterList = (new lmbDbTypeInfo)->getColumnTypeAccessors();
    $setter = $setterList[$type];
    $this->assertNotNull($setter);

    $sql = '
          INSERT INTO standard_types (
              "'.$column.'"
          ) VALUES (
              :'.$column.':
          )';
    $stmt = $this->connection->newStatement($sql);

    $stmt->$setter($column, $value);

    $id = $stmt->insertId('id');

    $sql = 'SELECT * FROM standard_types WHERE "id" = :id:';
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();

    return $record;
  }


  function checkSmallIntValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setSmallInt('literal', $value);
    $this->assertEquals($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_SMALLINT, 'type_smallint', $value);
    $this->assertEquals($record->getInteger('type_smallint'), $value);
    $this->assertEquals($record->get('type_smallint'), $value);
  }

  function checkIntegerValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setInteger('literal', $value);
    $this->assertEquals($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_INTEGER, 'type_integer', $value);
    $this->assertEquals($record->getInteger('type_integer'), $value);
    $this->assertEquals($record->get('type_integer'), $value);
  }

  function checkBooleanValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setBoolean('literal', $value);
    $this->assertEquals($stmt->getOneValue(), is_null($value) ? null : ($value ? 'TRUE' : 'FALSE'));

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BOOLEAN, 'type_boolean', $value);

    if(is_null($value))
      $this->assertEquals(null, $record->getBoolean('type_boolean'));
    else
      $this->assertEquals($record->getBoolean('type_boolean'), (boolean) $value);
  }

  function checkFloatValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setFloat('literal', $value);
    $this->assertEquals($stmt->getOneValue(), (float) $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_FLOAT, 'type_float', $value);

    if(is_null($value))
      $this->assertEquals(null, $record->getFloat('type_float'));
    else
      $this->assertEquals(round($record->getFloat('type_float'), 2), round((float) $value, 2));

    $this->assertEquals(round($record->get('type_float'), 2), round($value, 2));
  }

  function checkDoubleValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setDouble('literal', $value);
    $this->assertEquals($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DOUBLE, 'type_double', $value);

    if(is_string($value))
      $this->assertEquals($record->getStringFixed('type_double'), $value);
    else
      $this->assertEquals(round($record->getFloat('type_double'), 2), round($value, 2));

    $this->assertEquals(round($record->get('type_double'), 2), round($value, 2));
  }

  function checkDecimalValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setDecimal('literal', $value);
    $this->assertEquals(round($stmt->getOneValue(), 2), is_null($value) ? 'null' : round($value, 2));

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DECIMAL, 'type_decimal', $value);
    $this->assertEquals($record->getStringFixed('type_decimal'), is_null($value) ? 0 : $value);
    $this->assertEquals(round($record->get('type_decimal'), 2), is_null($value) ? 0 : round($value, 2));
  }

  function testSetDecimal()
  {
    $this->checkDecimalValue(0);
    $this->checkDecimalValue((float) 0);
    $this->checkDecimalValue(null);
    $this->checkDecimalValue(3.14);
    $this->checkDecimalValue('3.14');
    $this->checkDecimalValue('123456789012345678.01'); // To big for float
  }


  function testSetChar()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $string_list = array("Hello 'World!'",
          '"', '\'', '\\', '\\"', '\\\'', '\\0', '\\1',
          "%", "_", '&', '<', '>', '$', '`');
    foreach($string_list as $value)
    {
      $stmt->setChar('literal', $value);
      $this->assertEquals($stmt->getOneValue(), $value);
    }

    foreach($string_list as $value)
    {
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
    foreach($string_list as $value)
    {
      $stmt->setVarChar('literal', $value);
      $this->assertEquals($stmt->getOneValue(), $value);
    }

    foreach($string_list as $value)
    {
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

  }


  function testBlob()
  {
    //$stmt = $this->connection->newStatement('SELECT :literal:');

    $string_list = array("Hello 'World!'",
          '"', '\'', '\\', '\\"', '\\\'', '\\0', '\\1',
          "%", "_", '&', '<', '>', '$', '`');

    foreach($string_list as $value)
    {
      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BLOB, 'type_blob', $value);
      $this->assertEquals($record->getString('type_blob'), $value);
      $this->assertEquals($record->get('type_blob'), $value);
    }

    $value = null;
    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BLOB, 'type_blob', $value);
    $this->assertEquals($record->getString('type_blob'), is_null($value) ? '' : $value);
    $this->assertEquals($record->get('type_blob'), $value);

  }

  function testCharset()
  {
    $string_list = array("Текст", "ЁЁЁЁЁЁЁ");

    foreach($string_list as $value)
    {
      $value = mb_convert_encoding($value, $this->connection->getMbCharset(), 'UTF-8');

      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_VARCHAR, 'type_varchar', $value);
      $this->assertEquals($record->getString('type_varchar'), $value);
      $this->assertEquals($record->get('type_varchar'), $value);

      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BLOB, 'type_blob', $value);
      $this->assertEquals($record->getString('type_blob'), $value);
      $this->assertEquals($record->get('type_blob'), $value);

    }
  }


  function testSetDate()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setDate('literal', $value);
    $this->assertEquals(null, $stmt->getOneValue());

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
    $this->assertEquals(null, $record->getStringDate('type_date'));
    $this->assertEquals(null, $record->get('type_date'));

    $value = '2009-12-28';

    $stmt->setDate('literal', $value);
    $this->assertEquals($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
    $this->assertEquals($record->getStringDate('type_date'), $value);
    $this->assertEquals($record->get('type_date'), $value . " 00:00:00");

    $value = '1941-12-07';

    $stmt->setDate('literal', $value);
    $this->assertEquals($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
    $this->assertEquals($record->getStringDate('type_date'), $value);
    $this->assertEquals($record->get('type_date'), $value . " 00:00:00");

    $value = 'Bad Date Value';
  }

  function testSetTime()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setTime('literal', $value);
    $this->assertEquals(null, $stmt->getOneValue());

    $value = null;
    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
    $this->assertEquals($record->getString('type_time'), $value);
    $this->assertEquals($record->get('type_time'), $value);

    $value = '06:01:01';

    $stmt->setTime('literal', $value);
    $this->assertEquals($stmt->getOneValue(), date('Y-m-d') . " " . $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
    $this->assertEquals($record->getStringTime('type_time'), $value);
    $this->assertEquals($record->get('type_time'), date('Y-m-d') . " " . $value);

    $value = '18:01:01';

    $stmt->setTime('literal', $value);
    $this->assertEquals($stmt->getOneValue(), date('Y-m-d') . " " . $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
    $this->assertEquals($record->getStringTime('type_time'), $value);
    $this->assertEquals($record->get('type_time'), date('Y-m-d') . " " . $value);

    $value = 'Bad Time Value';
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
  }


  function testSetNull()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setNull('literal');
    $this->assertEquals(null, $stmt->getOneValue());

    $sql = '
          INSERT INTO standard_types (
              "type_smallint",
              "type_integer",
              "type_boolean",
              "type_char",
              "type_varchar",
              "type_clob",
              "type_float",
              "type_double",
              "type_decimal",
              "type_timestamp",
              "type_date",
              "type_time",
              "type_blob"
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
          )';
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

    $sql = 'SELECT * FROM standard_types WHERE "id" = :id:';
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();

    /* generic gets */
    $this->assertEquals(0, $record->get('type_smallint'));
    $this->assertEquals(0, $record->get('type_integer'));
    $this->assertEquals(0, $record->get('type_boolean'));
    $this->assertEquals(0, $record->get('type_char'));
    $this->assertEquals(0, $record->get('type_varchar'));
    $this->assertEquals(0, $record->get('type_clob'));
    $this->assertEquals(0, $record->get('type_float'));
    $this->assertEquals(0, $record->get('type_double'));
    $this->assertEquals(0, $record->get('type_decimal'));
    $this->assertEquals(0, $record->get('type_timestamp'));
    $this->assertEquals(0, $record->get('type_date'));
    $this->assertEquals(0, $record->get('type_time'));
    $this->assertEquals(0, $record->get('type_blob'));

    /* typed gets */
    $this->assertEquals(0, $record->getInteger('type_smallint'));
    $this->assertEquals(0, $record->getInteger('type_integer'));
    $this->assertEquals(0, $record->getBoolean('type_boolean'));
    $this->assertEquals(0, $record->getString('type_char'));
    $this->assertEquals(0, $record->getString('type_varchar'));
    $this->assertEquals(0, $record->getString('type_clob'));
    $this->assertEquals(0, $record->getFloat('type_float'));
    $this->assertEquals(0, $record->getStringFixed('type_double'));
    $this->assertEquals(0, $record->getStringFixed('type_decimal'));
    $this->assertEquals(0, $record->getStringTimeStamp('type_timestamp'));
    $this->assertEquals(0, $record->getStringDate('type_date'));
    $this->assertEquals(0, $record->getStringTime('type_time'));
    $this->assertEquals(0, $record->getString('type_blob'));
  }
}
