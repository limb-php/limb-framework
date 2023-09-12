<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\dbal\cases\nondriver;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\lmbSimpleDb;
use limb\toolkit\src\lmbToolkit;

class lmbSimpleDbTest extends TestCase
{
  var $db = null;
  var $conn = null;

  function setUp(): void
  {
    $toolkit = lmbToolkit::instance();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown(): void
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $stmt = $this->conn->newStatement('DELETE FROM test_db_table');
    $stmt->execute();
  }

  function testGetType()
  {
    $this->assertEquals($this->db->getType(), $this->conn->getType());
    $this->assertNotNull($this->db->getType());
  }

  function testInsert()
  {
    $id = $this->db->insert('test_db_table', array('title' =>  'wow',
                                                   'description' => 'wow!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEquals($record->get('title'), 'wow');
    $this->assertEquals($record->get('description'), 'wow!');
    $this->assertEquals($record->get('id'), $id);
  }

  //we test sequence based fields here
  function testInsertPrimaryKeyValue()
  {
    $id = $this->db->insert('test_db_table', array(/*'id' => 20,*/
                                                   'title' =>  'wow',
                                                   'description' => 'wow!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEquals($record->get('title'), 'wow');
    $this->assertEquals($record->get('description'), 'wow!');
    //$this->assertEquals("$id", '20');
    $this->assertEquals($record->get('id'), $id);
  }

  function testUpdateAll()
  {
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description'));
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description2'));

    $this->assertEquals($this->db->countAffected(), 0);
    $this->db->update('test_db_table', array('description' =>  'new_description'));
    $this->assertEquals($this->db->countAffected(), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEquals($record->get('description'), 'new_description');

    $records->next();
    $record = $records->current();
    $this->assertEquals($record->get('description'), 'new_description');
  }

  function testUpdateByCondition()
  {
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description'));
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description2'));
    $this->db->insert('test_db_table', array('title' =>  'yo', 'description' => 'description3'));

    $this->assertEquals($this->db->countAffected(), 0);
    $this->db->update('test_db_table',
                      array('description' =>  'new_description', 'title' => 'wow2'),
                      new lmbSQLFieldCriteria('title', 'wow'));
    $this->assertEquals($this->db->countAffected(), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table ORDER BY " . $this->conn->quoteIdentifier('id'));
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEquals($record->get('description'), 'new_description');
    $this->assertEquals($record->get('title'), 'wow2');

    $records->next();
    $record = $records->current();
    $this->assertEquals($record->get('description'), 'new_description');
    $this->assertEquals($record->get('title'), 'wow2');
  }

  function testSelectWithOrder()
  {
    $data = array(
      0 => array('title' =>  'aaa', 'description' => 'description1'),
      1 => array('title' =>  'zzz', 'description' => 'description2'),
      2 => array('title' =>  'kkk', 'description' => 'description3')
    );

    $this->db->insert('test_db_table', $data[0]);
    $this->db->insert('test_db_table', $data[1]);
    $this->db->insert('test_db_table', $data[2]);

    $result = $this->db->select('test_db_table', null, array('title' => 'DESC'))->getArray();
    $this->assertEquals($result[0]->get('title'), 'zzz');
    $this->assertEquals($result[1]->get('title'), 'kkk');
    $this->assertEquals($result[2]->get('title'), 'aaa');
  }

  function testDeleteAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description1'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db->insert('test_db_table', $data[0]);
    $this->db->insert('test_db_table', $data[1]);

    $this->assertEquals($this->db->countAffected(), 0);
    $this->db->delete('test_db_table');
    $this->assertEquals($this->db->countAffected(), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEquals($records->count(), 0);
  }

  function testDeleteByStringCondition()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db->insert('test_db_table', $data[0]);
    $this->db->insert('test_db_table', $data[1]);

    $this->assertEquals($this->db->countAffected(), 0);
    $this->db->delete('test_db_table',new lmbSQLFieldCriteria('title', 'wow'));
    $this->assertEquals($this->db->countAffected(), 1);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEquals($records->count(), 1);
  }

  function testExecute()
  {
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description'));

    $this->assertEquals($this->db->select('test_db_table')->count(), 1);

    $this->db->execute("DELETE FROM test_db_table");

    $this->assertEquals($this->db->select('test_db_table')->count(), 0);
  }

  function testQuery()
  {
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'descr'));
    $arr = $this->db->query("SELECT * from test_db_table")->getArray();
    $this->assertEquals(sizeof($arr), 1);
    $this->assertEquals($arr[0]["title"], 'wow');
    $this->assertEquals($arr[0]["description"], 'descr');
  }

  function testQuote()
  {
     $this->assertEquals($this->db->quote('foo'), $this->conn->quoteIdentifier('foo'));
  }
}
