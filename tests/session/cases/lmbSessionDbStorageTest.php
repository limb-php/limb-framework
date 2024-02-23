<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\session\cases;

require_once(dirname(__FILE__) . '/.setup.php');

use limb\dbal\src\lmbTableGateway;
use PHPUnit\Framework\TestCase;
use limb\session\src\lmbSessionDbStorage;
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
        $this->db = new lmbTableGateway('lmb_session', $this->conn);
        $this->db->setPrimaryKeyName('session_id');

        $this->db->delete();

        $this->driver = new lmbSessionDbStorage($this->conn);
    }

    function tearDown(): void
    {
        $this->db->delete();

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
        $id = 'fghprty121as';
        $data = 'global_user|O:4:"user":12:{s:3:"_id";...';

        $this->db->insert([
                'session_id' => $id,
                'session_data' => $data,
                'last_activity_time' => 10]
        );

        $this->db->insert(array(
                'session_id' => 'junk',
                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                'last_activity_time' => 10)
        );

        $this->assertEquals($data, $this->driver->storageRead($id));
    }

    function testStorageReadBadSessionId()
    {
        $this->assertEquals("", $this->driver->storageRead("'bad';DROP lmb_session;"));
    }

    function testStorageReadFalse()
    {
        $this->db->insert([
                'session_id' => 'junk',
                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                'last_activity_time' => 10]
        );


        $this->assertEquals('', $this->driver->storageRead('no_such_session'));
    }

    function testStorageWriteInsert()
    {
        $value = 'whatever';
        $id = 20;

        $this->driver->storageWrite($id, $value);

        $rs = $this->db->select();

        $this->assertEquals(1, $rs->count());

        $rs->rewind();
        $record = $rs->current();

        $this->assertEquals($id, $record->get('session_id'));
        $this->assertEquals($value, $record->getBlob('session_data'));
        $this->assertTrue($record->get('last_activity_time') > 0 && $record->get('last_activity_time') <= time());
    }

    function testStorageWriteUpdate()
    {
        $id = 'fghprty121as';
        $value = 'global_user|O:4:"user":12:{s:3:"_id";...';
        $time = 10;

        $this->db->insert(array(
                'session_id' => $id,
                'session_data' => $value,
                'last_activity_time' => $time
            )
        );

        $this->driver->storageWrite($id, $value);

        $rs = $this->db->select();

        $this->assertEquals(1, $rs->count());

        $rs->rewind();
        $record = $rs->current();

        $this->assertEquals($id, $record->get('session_id'));
        $this->assertEquals($value, $record->getBlob('session_data'));
        $this->assertTrue($record->get('last_activity_time') > $time && $record->get('last_activity_time') <= time());
    }

    function testStorageWriteInsertBadSessionId()
    {
        $id = "'fghprty121as';SELECT * FROM test;";
        $value = "'data';DROP lmb_session;";

        $this->driver->storageWrite($id, $value);

        $rs = $this->db->select();
        $rs->rewind();
        $record = $rs->current();

        $this->assertEquals($id, $record->get('session_id'));
        $this->assertEquals($value, $record->getBlob('session_data'));
    }

    function testStorageWriteUpdateBadSessionId()
    {
        $this->db->insert(
            array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                'session_data' => $value = "'data';DROP lmb_session;")
        );

        $this->driver->storageWrite($id, $value);

        $rs = $this->db->select();
        $rs->rewind();
        $record = $rs->current();

        $this->assertEquals($id, $record->get('session_id'));
        $this->assertEquals($value, $record->getBlob('session_data'));
    }

    function testStorageDestroy()
    {
        $id = "'fghprty121as';SELECT * FROM test;";

        $this->db->insert(
            array(
                'session_id' => $id,
                'session_data' => "data")
        );

        $this->db->insert(
            array(
                'session_id' => 'junk',
                'session_data' => 'junk')
        );

        $this->driver->storageDestroy($id);

        $rs = $this->db->select();

        $this->assertEquals(1, $rs->count());
        $rs->rewind();
        $record = $rs->current();
        $this->assertEquals('junk', $record->get('session_id'));
    }

    function testStorageGcTrue()
    {
        $this->db->insert(
            array(
                'session_id' => "whatever",
                'session_data' => "data",
                'last_activity_time' => time() - 301)
        );

        $this->driver->storageGc(300);

        $rs = $this->db->select();
        $rs->rewind();
        $this->assertFalse($rs->valid());
    }

    function testStorageGcUseSettedMaxLifeTime()
    {
        $driver = new lmbSessionDbStorage($this->conn, $max_life_time = 500);

        $this->db->insert(
            array(
                'session_id' => "whatever",
                'session_data' => "data",
                'last_activity_time' => time() - 400)
        );

        $driver->storageGc(300);

        $rs = $this->db->select();
        $rs->rewind();
        $this->assertTrue($rs->valid());
    }

    function testStorageGcFalse()
    {
        $this->db->insert(
            array(
                'session_id' => "whatever",
                'session_data' => "data",
                'last_activity_time' => time() - 298)
        );

        $this->driver->storageGc(300);

        $rs = $this->db->select();
        $rs->rewind();
        $this->assertFalse(!$rs->valid());
    }
}
