<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\nondriver\dump;

use limb\core\src\lmbEnv;
use PHPUnit\Framework\TestCase;
use limb\dbal\src\lmbSimpleDb;
use limb\dbal\src\dump\lmbSQLDumpLoader;
use limb\toolkit\src\lmbToolkit;

require_once(dirname(__FILE__) . '/../.setup.php');

abstract class lmbSQLDumpLoaderTestCase extends TestCase
{
    var $db;
    var $conn;
    var $file_path;

    function setUp(): void
    {
        $toolkit = lmbToolkit::instance();
        $this->conn = $toolkit->getDefaultDbConnection();
        $this->db = new lmbSimpleDb($this->conn);

        $sql = <<<EOD
/* test_one_table_object table records */
insert into test_one_table_object (id, annotation, content) values (1, 'whatever;', 'whatever;');
insert into test_one_table_object (id, annotation, content) values (2, 'whatever;', 'whatever;');

/* test_db_table table records */
insert into test_db_table (id, description, title) values (1, 'whatever;', 'whatever;');
insert into test_db_table (id, description, title) values (2, 'whatever;', 'whatever;');
EOD;

        $this->file_path = lmbEnv::get('LIMB_VAR_DIR') . '/sql_dump_loader.tmp';

        $this->_writeDump($sql, $this->file_path);

        $this->_dbCleanUp();
    }

    function tearDown(): void
    {
        unlink($this->file_path);
        $this->_dbCleanUp();
    }

    function _createLoader($file = null)
    {
        return new lmbSQLDumpLoader($file);
    }

    function _writeDump($sql, $file)
    {
        $fh = fopen($file, 'w');
        fwrite($fh, $sql);
        fclose($fh);
    }

    function _dbCleanUp()
    {
        $this->db->delete('test_one_table_object');
        $this->db->delete('test_db_table');
    }

    function testCreate()
    {
        $loader = $this->_createLoader($this->file_path);
        $this->assertEquals(array('test_one_table_object', 'test_db_table'), $loader->getAffectedTables());
        $this->assertEquals(4, sizeof($loader->getStatements()));
    }

    function testCreateWithEmptyFile()
    {
        $loader = $this->_createLoader();
        $this->assertEquals(array(), $loader->getAffectedTables());
        $this->assertEquals(array(), $loader->getStatements());
    }

    function testLoad()
    {
        $loader = $this->_createLoader();
        $loader->loadFile($this->file_path);
        $this->assertEquals(array('test_one_table_object', 'test_db_table'), $loader->getAffectedTables());
        $this->assertEquals(4, sizeof($loader->getStatements()));
    }

    function testLoadTwice()
    {
        $loader = $this->_createLoader();
        $loader->loadFile($this->file_path);
        $this->assertEquals(array('test_one_table_object', 'test_db_table'), $loader->getAffectedTables());
        $this->assertEquals(4, sizeof($loader->getStatements()));

        $new_sql = <<<EOD
insert into foo (id, annotation, content) values (1, 'whatever;', 'whatever;');
EOD;

        $second_file = lmbEnv::get('LIMB_VAR_DIR') . '/sql_dump_loader.new';
        $this->_writeDump($new_sql, $second_file);

        $loader->loadFile($second_file);

        $this->assertEquals(array('foo'), $loader->getAffectedTables());
        $this->assertEquals(1, sizeof($loader->getStatements()));
    }

    function testExecute()
    {
        $loader = $this->_createLoader($this->file_path);
        $this->assertEquals(array('test_one_table_object', 'test_db_table'), $loader->getAffectedTables());

        $loader->execute($this->conn);

        $rs1 = $this->db->select('test_one_table_object');
        $this->assertEquals(2, $rs1->count());

        $rs2 = $this->db->select('test_db_table');
        $this->assertEquals(2, $rs2->count());
    }

    function testExecutePattern()
    {
        $loader = $this->_createLoader($this->file_path);

        $loader->execute($this->conn, '/test_one_table_object/');

        $rs1 = $this->db->select('test_one_table_object');
        $this->assertEquals(2, $rs1->count());

        $rs2 = $this->db->select('test_db_table');
        $this->assertEquals(0, $rs2->count());
    }

    function testFreeDataBase()
    {
        $this->db->insert('test_one_table_object', array('id' => 10,
            'annotation' => 'some annotation',
            'content' => 'some content'));

        $this->db->insert('test_db_table', array('id' => 10,
            'description' => 'some description',
            'title' => 'some title'));


        $loader = $this->_createLoader($this->file_path);
        $this->assertEquals(array('test_one_table_object', 'test_db_table'), $loader->getAffectedTables());

        $loader->cleanTables($this->conn);

        $rs1 = $this->db->select('test_one_table_object');
        $this->assertEquals(0, $rs1->count());

        $rs2 = $this->db->select('test_db_table');
        $this->assertEquals(0, $rs2->count());
    }
}
