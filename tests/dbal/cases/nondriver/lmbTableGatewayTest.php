<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\nondriver;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\drivers\lmbDbTypeInfo;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\lmbTableGateway;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;
use limb\dbal\src\query\lmbInsertOnDuplicateUpdateQuery;

class lmbTableGatewayTest extends TestCase
{
  var $conn = null;
  var $db_table_test = null;

  function setUp(): void
  {
    $toolkit = lmbToolkit::save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db_table_test = new lmbTableGateway('test_db_table', $this->conn);

    $this->_cleanUp();
  }

  function tearDown(): void
  {
    $this->_cleanUp();

    lmbToolkit::restore();
  }

  function _cleanUp()
  {
    $stmt = $this->conn->newStatement('DELETE FROM test_db_table');
    $stmt->execute();
  }

  function testCorrectTableProperties()
  {
    $this->assertEquals('test_db_table', $this->db_table_test->getTableName());
    $this->assertEquals('id', $this->db_table_test->getPrimaryKeyName());
    $this->assertEquals(lmbDbTypeInfo::TYPE_INTEGER, $this->db_table_test->getColumnType('id'));
    $this->assertEquals(false, $this->db_table_test->getColumnType('no_column'));
    $this->assertTrue($this->db_table_test->hasColumn('id'));
    $this->assertTrue($this->db_table_test->hasColumn('description'));
    $this->assertTrue($this->db_table_test->hasColumn('title'));
    $this->assertFalse($this->db_table_test->hasColumn('no_such_a_field'));
  }

  function testInsert()
  {
    $id = $this->db_table_test->insert(array('title' =>  'wow',
                                             'description' => 'wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEquals('wow', $record->get('title'));
    $this->assertEquals('wow!', $record->get('description'));
    $this->assertEquals($record->get('id'), $id);
  }

  function testInsertOnDuplicateKeyUpdate()
  {
    $current_connection = lmbToolkit::instance()->getDefaultDbConnection();
    $is_supported = lmbInsertOnDuplicateUpdateQuery::isSupportedByDbConnection($current_connection);
    if(!$is_supported)
    {
      echo "Skip: ".$current_connection->getType()." not support insert on duplicate update queries \n";
      return;
    }

    $id = $this->db_table_test->insertOnDuplicateUpdate(array('title' =>  'wow',
                                             'description' => 'wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEquals('wow', $record->get('title'));
    $this->assertEquals('wow!', $record->get('description'));
    $this->assertEquals($record->get('id'), $id);

    $id = $this->db_table_test->insertOnDuplicateUpdate(array('id' => $id,
                                             'title' =>  'wow',
                                             'description' => 'new wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEquals('wow', $record->get('title'));
    $this->assertEquals('new wow!', $record->get('description'));
    $this->assertEquals($record->get('id'), $id);

    $this->assertTrue(true);
  }

//  function testInsertUpdatesSequenceIfAutoIncrementFieldWasSet()
//  {
//    $id = $this->db_table_test->insert(array('id' => 4, 'title' =>  'wow', 'description' => 'wow!'));
//    $this->assertEquals($id, 4);
//  }

  function testInsertThrowsExceptionIfAllFieldsWereFiltered()
  {
    try
    {
      $this->db_table_test->insert(array('junk!!!' => 'junk!!!'));
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testUpdateAll()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));

    $updated_rows_count = $this->db_table_test->update(array('description' =>  'new_description', 'junk!!!' => 'junk!!!'));

    $this->assertEquals(2, $this->db_table_test->getAffectedRowCount());
    $this->assertEquals(2, $updated_rows_count);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEquals('new_description', $record->get('description'));

    $records->next();
    $record = $records->current();
    $this->assertEquals('new_description', $record->get('description'));
  }

  function testUpdateAllWithRawSet()
  {
    $this->db_table_test->insert(array('ordr' =>  '1'));
    $this->db_table_test->insert(array('ordr' =>  '10'));

    $raw_criteria = $this->conn->quoteIdentifier('ordr') . '=' . $this->conn->quoteIdentifier('ordr') . '+1';
    $updated_rows_count = $this->db_table_test->update($raw_criteria);

    $this->assertEquals(2, $this->db_table_test->getAffectedRowCount());
    $this->assertEquals(2, $updated_rows_count);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEquals('2', $record->get('ordr'));

    $records->next();
    $record = $records->current();
    $this->assertEquals('11', $record->get('ordr'));
  }

  function testUpdateByCriteria()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));//should be changed
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));//should be changed
    $this->db_table_test->insert(array('title' =>  'yo', 'description' => 'description3'));

    $updated_rows_count = $this->db_table_test->update(
      array('description' =>  'new_description', 'title' => 'wow2', 'junk!!!' => 'junk!!!'),
      new lmbSQLFieldCriteria('title', 'wow')
    );

    $this->assertEquals(2, $this->db_table_test->getAffectedRowCount());
    $this->assertEquals(2, $updated_rows_count);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table ORDER BY " . $this->conn->quoteIdentifier('id'));
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEquals('new_description', $record->get('description'));
    $this->assertEquals('wow2', $record->get('title'));

    $records->next();
    $record = $records->current();
    $this->assertEquals('new_description', $record->get('description'));
    $this->assertEquals('wow2', $record->get('title'));
  }

  function testUpdateById()
  {
    $id = $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow2', 'description' => 'description2'));

    $this->db_table_test->updateById($id, array('description' =>  'new_description'));

    $this->assertEquals(1, $this->db_table_test->getAffectedRowCount());

    $stmt = $this->conn->newStatement('SELECT * FROM test_db_table WHERE ' . $this->conn->quoteIdentifier('id') . '=' . $id);
    $records = $stmt->getRecordSet();
    $records->rewind();
    $record = $records->current();
    $this->assertEquals('new_description', $record->get('description'));
  }

  function testSelectAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->select();

    $this->assertEquals(2, $result->count());

    $result->rewind();
    $record = $result->current();
    $this->assertEquals('description', $record->get('description'));

    $result->next();
    $record = $result->current();
    $this->assertEquals('description2', $record->get('description'));
  }

  function testSelectAllLimitFields()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));

    $result = $this->db_table_test->select(null, array(), array('title'));

    $this->assertEquals(1, $result->count());

    $this->assertEquals('wow', $result->at(0)->get('title'));
    $this->assertNull($result->at(0)->get('description'));
  }

  function testSelectRecordById()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $id = $this->db_table_test->insert($data[1]);

    $record = $this->db_table_test->selectRecordById($id);
    $this->assertEquals('description2', $record->get('description'));
  }

  function testSelectRecordByIdLimitFields()
  {
    $id = $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));

    $record = $this->db_table_test->selectRecordById($id, array('title'));
    $this->assertEquals('wow', $record->get('title'));
    $this->assertNull($record->get('description'));
  }

  function testSelectRecordByIdNotFound()
  {
    $this->assertNull($this->db_table_test->selectRecordById(1));
  }

  function testSelectFirstRecord()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $record = $this->db_table_test->selectFirstRecord();
    $this->assertEquals('wow', $record->get('title'));
  }

  function testSelectFirstRecordLimitFields()
  {
    $id = $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));

    $record = $this->db_table_test->selectFirstRecord(null, array(), array('title'));
    $this->assertEquals('wow', $record->get('title'));
    $this->assertNull($record->get('description'));
  }

  function testSelectFirstRecordReturnNullIfNothingIsFound()
  {
    $this->assertNull($this->db_table_test->selectFirstRecord($this->conn->quoteIdentifier('id') . '= -10000'));
  }

  function testDeleteAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->delete();

    $this->assertEquals(2, $this->db_table_test->getAffectedRowCount());

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEquals(0, $records->count());
  }

  function testDeleteByCriteria()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->delete(new lmbSQLFieldCriteria('title', 'hey'));

    $this->assertEquals(0, $this->db_table_test->getAffectedRowCount());

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEquals(2, $records->count());
  }

  function testDeleteById()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $id = $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->deleteById($id);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEquals(1, $records->count());

    $records->rewind();

    $record = $records->current();
    $this->assertEquals('wow!', $record->get('title'));
  }

  function testGetColumnsForSelectDefaultName()
  {
    $this->assertEquals(array('test_db_table.id' => 'id',
                                                                          'test_db_table.description' => 'description',
                                                                          'test_db_table.title' => 'title',
                                                                          'test_db_table.ordr' => 'ordr'), $this->db_table_test->getColumnsForSelect());
  }

  function testGetColumnsForSelectSpecificNameAndPrefix()
  {
    $this->assertEquals(array('tn.id' => '_id',
         'tn.description' => '_description',
         'tn.title' => '_title',
         'tn.ordr' => '_ordr'),
        $this->db_table_test->getColumnsForSelect('tn', array(), '_'));
  }

  function testGetColumnsForSelectSpecificNameWithExcludes()
  {
    $this->assertEquals(array('tn.title' => 'title', 'tn.ordr' => 'ordr'),
        $this->db_table_test->getColumnsForSelect('tn', array('id', 'description')));

  }
}
