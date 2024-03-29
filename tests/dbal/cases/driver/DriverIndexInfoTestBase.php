<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver;

use limb\dbal\src\drivers\lmbDbTableInfo;

abstract class DriverIndexInfoTestBase extends DriverMetaTestBase
{
    /**
     * @var lmbDbTableInfo
     */
    protected $table;

    protected $_index_names = array(
        'primary' => 'primary_column',
        'unique' => 'unique_column_named_index',
        'common' => 'common_column',
    );

    function setUp(): void
    {
        $dbinfo = $this->connection->getDatabaseInfo();
        $this->table = $dbinfo->getTable('indexes');
    }

    function testFetchingIndexes()
    {
        $indexes = $this->table->getIndexList();

        $this->assertCount(3, $indexes);

        $this->assertTrue($this->table->hasIndex($this->_index_names['primary']));
        $primary_index = $this->table->getIndex($this->_index_names['primary']);
        $this->assertTrue($primary_index->isPrimary());
        $this->assertEquals('primary_column', $primary_index->column_name);

        $this->assertTrue($this->table->hasIndex($this->_index_names['unique']));
        $unique_index = $this->table->getIndex($this->_index_names['unique']);
        $this->assertTrue($unique_index->isUnique());
        $this->assertEquals('unique_column', $unique_index->column_name);

        $this->assertTrue($this->table->hasIndex($this->_index_names['common']));
        $common_index = $this->table->getIndex($this->_index_names['common']);
        $this->assertTrue($common_index->isCommon());
        $this->assertEquals('common_column', $common_index->column_name);
    }

    function tearDown(): void
    {
        unset($this->table);
        parent::tearDown();
    }
}
