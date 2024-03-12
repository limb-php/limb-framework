<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\nondriver\query;

require_once(dirname(__FILE__) . '/../init.inc.php');

use limb\dbal\src\query\lmbBulkInsertQuery;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;

class lmbBulkInsertQueryTest extends lmbQueryBaseTestCase
{
    function setUp(): void
    {
        parent::setUp();

        $current_connection = lmbToolkit::instance()->getDefaultDbConnection();
        if (!lmbBulkInsertQuery::isSupportedByDbConnection($current_connection))
            $this->markTestSkipped();
    }

    function testInsert()
    {
        $query = new lmbBulkInsertQuery('test_db_table', $this->conn);
        $query->addSet(array('id' => 2, 'title' => 'some title', 'description' => 'some description'));
        $query->addSet(array('id' => 4, 'title' => 'some other title', 'description' => 'some other description'));
        $stmt = $query->getStatement();
        $stmt->execute();

        $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
        $arr = $rs->getArray();

        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals(2, $arr[0]['id']);
        $this->assertEquals('some title', $arr[0]['title']);
        $this->assertEquals('some description', $arr[0]['description']);
        $this->assertEquals(4, $arr[1]['id']);
        $this->assertEquals('some other description', $arr[1]['description']);
        $this->assertEquals('some other title', $arr[1]['title']);
    }

    function testExecuteDoesNothingIfNotSetsSpecified()
    {
        $query = new lmbBulkInsertQuery('test_db_table', $this->conn);
        $query->execute();

        $this->assertTrue(true);
    }

    function testGetStatementThrowsExceptionIfNotSetsSpecified()
    {
        $query = new lmbBulkInsertQuery('test_db_table', $this->conn);
        try {
            $query->getStatement();
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

}
