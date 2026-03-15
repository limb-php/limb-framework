<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

use limb\core\src\lmbEnv;
use limb\dbal\src\drivers\lmbDbCachedInfo;
use limb\dbal\src\drivers\lmbDbTableInfo;

abstract class DriverDatabaseInfoTestBase extends DriverMetaTestBase
{
    protected $dbinfo;

    function setUp(): void
    {
        $this->dbinfo = $this->connection->getDatabaseInfo();
    }

    function testHasTable()
    {
        $this->assertTrue($this->dbinfo->hasTable('founding_fathers'));
        $this->assertTrue($this->dbinfo->hasTable('standard_types'));
    }

    function testGetTable()
    {
        $table = $this->dbinfo->getTable('founding_fathers');
        $this->assertInstanceOf(lmbDbTableInfo::class, $table);
    }

    function testGetTables()
    {
        $tables = $this->dbinfo->getTables();
        $this->assertTrue(isset($tables['founding_fathers']));
        $this->assertInstanceOf(lmbDbTableInfo::class, $tables['founding_fathers']);
        $this->assertEquals('founding_fathers', $tables['founding_fathers']->getName());
    }

    function testDbInfo()
    {
        $cached_db_info = new lmbDbCachedInfo($this->connection, lmbEnv::get('LIMB_VAR_DIR')); // all data loaded

        $this->assertEquals($this->dbinfo->getName(), $cached_db_info->getName());
        $this->assertEquals($this->dbinfo->getTableList(), $cached_db_info->getTableList());
        $this->assertEquals($this->dbinfo->hasTable('founding_fathers'), $cached_db_info->hasTable('founding_fathers'));

        //$this->dbinfo->getTable('standard_types')->loadColumns();
        //$this->assertEquals($this->dbinfo->getTable('standard_types'), $cached_db_info->getTable('standard_types'));
    }
}
