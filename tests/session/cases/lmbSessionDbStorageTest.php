<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\session\cases;

use PHPUnit\Framework\TestCase;
use limb\session\src\lmbSessionDbStorage;
use limb\dbal\src\lmbSimpleDb;
use limb\toolkit\src\lmbToolkit;

class lmbSessionDbStorageTest extends TestCase
{
  protected $db;
  protected $conn;
  protected $driver;

  function setUp(): void
  {
    $toolkit = lmbToolkit::save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->db->delete('lmb_session');

    $this->driver = new lmbSessionDbStorage($this->conn);
  }

  function tearDown(): void
  {
    $this->db->delete('lmb_session');

    lmbToolkit::restore();
  }

  function testStorageOpen()
  {
    $this->assertTrue($this->driver->storageOpen());
  }

  function testStorageClose()
  {
    $this->assertTrue($this->driver->storageClose());
  }

  function testStorageReadOk()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $data = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10), null);

    $this->db->insert('lmb_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10), null);

    $this->assertEquals($data, $this->driver->storageRead($id));
  }

  function testStorageReadBadSessionId()
  {
    $this->assertFalse($this->driver->storageRead("'bad';DROP lmb_session;"));
  }

  function testStorageReadFalse()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10), null);


    $this->assertEquals(false, $this->driver->storageRead('no_such_session'));
  }

  function testStorageWriteInsert()
  {
    $value = 'whatever';
    $id = 20;

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');

    $this->assertEquals($rs->count(), 1);

    $rs->rewind();
    $record = $rs->current();

    $this->assertEquals($record->get('session_id'), $id);
    $this->assertEquals($record->get('session_data'), $value);
    $this->assertTrue($record->get('last_activity_time') > 0 &&  $record->get('last_activity_time') <= time());
  }

  function testStorageWriteUpdate()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $value = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => $time = 10), null);

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');

    $this->assertEquals($rs->count(), 1);

    $rs->rewind();
    $record = $rs->current();

    $this->assertEquals($record->get('session_id'), $id);
    $this->assertEquals($record->get('session_data'), $value);
    $this->assertTrue($record->get('last_activity_time') > $time &&  $record->get('last_activity_time') <= time());
  }

  function testStorageWriteInsertBadSessionId()
  {
    $id = "'fghprty121as';SELECT * FROM test;";
    $value = "'data';DROP lmb_session;";

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $record = $rs->current();

    $this->assertEquals($record->get('session_id'), $id);
    $this->assertEquals($record->get('session_data'), $value);
  }

  function testStorageWriteUpdateBadSessionId()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => $value = "'data';DROP lmb_session;"), null);

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $record = $rs->current();

    $this->assertEquals($record->get('session_id'), $id);
    $this->assertEquals($record->get('session_data'), $value);
  }

  function testStorageDestroy()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => "data"), null);

    $this->db->insert('lmb_session',
                          array('session_id' => 'junk',
                                'session_data' => 'junk'), null);

    $this->driver->storageDestroy($id);

    $rs = $this->db->select('lmb_session');

    $this->assertEquals($rs->count(), 1);
    $rs->rewind();
    $record = $rs->current();
    $this->assertEquals($record->get('session_id'), 'junk');
  }

  function testStorageGcTrue()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 301), null);

    $this->driver->storageGc(300);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testStorageGcUseSettedMaxLifeTime()
  {
    $driver = new lmbSessionDbStorage($this->conn, $max_life_time = 500);

    $this->db->insert('lmb_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 400), null);

    $driver->storageGc(300);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $this->assertTrue($rs->valid());
  }

  function testStorageGcFalse()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 298), null);

    $this->driver->storageGc(300);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $this->assertFalse(!$rs->valid());
  }
}
