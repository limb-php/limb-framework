<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\nondriver\query;

require_once(dirname(__FILE__) . '/../../init.inc.php');

use limb\dbal\src\query\lmbInsertOnDuplicateUpdateQuery;
use limb\toolkit\src\lmbToolkit;

class lmbInsertOnDuplicateUpdateQueryTest extends lmbQueryBaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/../.setup.php');
    }

    function setUp(): void
    {
        parent::setUp();

        $current_connection = lmbToolkit::instance()->getDefaultDbConnection();
        if (!lmbInsertOnDuplicateUpdateQuery::isSupportedByDbConnection($current_connection))
            $this->markTestSkipped('Insert On Duplicate Update not supported. Test skipped.');
    }

    function testInsert()
    {
        $startId = $this->db->insert('test_db_table', array('description' => 'text1'));

        $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
        $query->addField('id', $id = $startId + 1);
        $query->addField('description', $description = 'Some \'description\'');
        $query->addField('title', $title = 'Some title');

        $stmt = $query->getStatement();
        $stmt->execute();

        $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
        $arr = $rs->getArray();

        $this->assertEquals(sizeof($arr), 2);
        $this->assertEquals($arr[0]['id'], $startId);
        $this->assertEquals($arr[1]['id'], $id);
        $this->assertEquals($arr[1]['description'], $description);
        $this->assertEquals($arr[1]['title'], $title);
    }

    function testUpdate()
    {
        $startId = $this->db->insert('test_db_table', array('description' => 'text1'));

        $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
        $query->addField('id', $id = $startId + 1);
        $query->addField('description', 'Some \'description\'');
        $query->addField('title', 'Some title');

        $stmt = $query->getStatement()->execute();

        $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
        $query->addField('id', $id);
        $query->addField('description', $description = 'Some another \'description\'');
        $query->addField('title', $title = 'Some another title');

        $stmt = $query->getStatement()->execute();

        $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
        $arr = $rs->getArray();

        $this->assertEquals(sizeof($arr), 2);
        $this->assertEquals($arr[0]['id'], $startId);
        $this->assertEquals($arr[1]['id'], $id);
        $this->assertEquals($arr[1]['description'], $description);
        $this->assertEquals($arr[1]['title'], $title);
    }

    function testAddFieldWithoutValueOnlyReservesAPlaceholder()
    {
        $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
        $query->addField('description');
        $query->addField('title');

        $stmt = $query->getStatement();
        $stmt->set('description', $description = 'Some \'description\'');
        $stmt->set('title', $title = 'Some title');
        $stmt->execute();

        $rs = $this->db->select('test_db_table');
        $arr = $rs->getArray();

        $this->assertEquals(sizeof($arr), 1);
        $this->assertEquals($arr[0]['description'], $description);
        $this->assertEquals($arr[0]['title'], $title);

    }
}
