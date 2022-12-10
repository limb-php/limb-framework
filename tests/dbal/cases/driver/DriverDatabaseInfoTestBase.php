<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver;

abstract class DriverDatabaseInfoTestBase extends DriverMetaTestBase
{
  var $dbinfo;

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
    $this->assertIsA($table, 'lmbDbTableInfo');
  }
  
  function testGetTables()
  {
    $tables = $this->dbinfo->getTables();
    if($this->assertTrue(isset($tables['founding_fathers'])))
      if($this->assertIsA($tables['founding_fathers'], 'lmbDbTableInfo'))
        $this->assertEquals($tables['founding_fathers']->getName(), 'founding_fathers');
  }
}
