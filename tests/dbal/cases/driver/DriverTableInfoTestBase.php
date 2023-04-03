<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\driver;

use limb\dbal\src\drivers\lmbDbColumnInfo;
use limb\dbal\src\drivers\lmbDbIndexInfo;
use limb\dbal\src\drivers\lmbDbInfo;
use limb\dbal\src\drivers\lmbDbTableInfo;
use limb\toolkit\src\lmbToolkit;

abstract class DriverTableInfoTestBase extends DriverMetaTestBase
{
  /**
   * @var lmbDBInfo
   */
  var $database_info;
  /**
   * @var lmbDBTableInfo
   */
  var $table_info;

  function setUp(): void
  {
    $this->database_info = $this->connection->getDatabaseInfo();
    $this->table_info = $this->database_info->getTable('founding_fathers');
  }

  function tearDown(): void
  {
    unset($this->table);
    parent::tearDown();
  }

  function testGetDatabase()
  {
    $db = $this->table_info->getDatabase();
    $this->assertInstanceOf(lmbDbInfo::class, $db);
  }

  function testGetName()
  {
    $this->assertEquals($this->table_info->getName(), 'founding_fathers');
  }

  function testHasColumn()
  {
    $this->assertTrue($this->table_info->hasColumn('id'));
    $this->assertTrue($this->table_info->hasColumn('first'));
    $this->assertTrue($this->table_info->hasColumn('last'));
    $this->assertFalse($this->table_info->hasColumn('tiabaltu'));
  }

  function testGetColumn()
  {
    $column = $this->table_info->getColumn('last');
    $this->assertInstanceOf(lmbDbColumnInfo::class, $column);
  }

  function testGetColumnList()
  {
    $this->assertEquals($this->table_info->getColumnList(),
          array('id' => 'id', 'first' => 'first', 'last' => 'last'));
  }
  
  function testGetColumns()
  {
    $columns = $this->table_info->getColumns();
    $this->assertTrue(isset($columns['id']));
    $this->assertInstanceOf(lmbDbColumnInfo::class, $columns['id']);
    $this->assertEquals($columns['id']->getName(), 'id');
  }


  function _isIndexImplemented()
  {
    $db_types_with_support = array('mysql', 'mysqli');

    $current_type = lmbToolkit::instance()->getDefaultDbConnection()->getType();

    return in_array($current_type, $db_types_with_support);
  }

  function testHasIndex()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $this->assertTrue($table->hasIndex('primary_column'));
    $this->assertFalse($table->hasIndex('tiabaltu_index'));
  }

  function testGetIndex()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $index = $table->getIndex('primary_column');
    $this->assertInstanceOf(lmbDbIndexInfo::class, $index);
  }

  function testGetIndexList()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $this->assertEquals($table->getIndexList(),
      array('primary_column' => 'primary_column',
            'unique_column_named_index' => 'unique_column_named_index',
            'common_column' => 'common_column'));
  }

  function testGetIndexForColumn()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $index = $table->getIndexForColumn('primary_column');
    $this->assertInstanceOf(lmbDbIndexInfo::class, $index);
    $this->assertIdentical('primary_column', $index->getName());

    $index = $table->getIndexForColumn('unique_column');
    $this->assertInstanceOf(lmbDbIndexInfo::class, $index);
    $this->assertIdentical('unique_column_named_index', $index->getName());
  }
}
