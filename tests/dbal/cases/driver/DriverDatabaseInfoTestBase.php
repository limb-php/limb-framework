<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver;

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
}
