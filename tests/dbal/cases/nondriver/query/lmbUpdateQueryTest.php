<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\nondriver\query;

require_once(dirname(__FILE__) . '/../../init.inc.php');

use limb\dbal\src\query\lmbUpdateQuery;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;

class lmbUpdateQueryTest extends lmbQueryBaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/../.setup.php');
    }

    function testUpdate()
    {
        $startId = $this->db->insert('test_db_table', array('description' => 'text1'));
        $this->db->insert('test_db_table', array('description' => 'text2'));

        $query = new lmbUpdateQuery('test_db_table', $this->conn);
        $query->addField('description', $description = 'Some description');
        $query->addField('title', $title = 'Some title');

        $stmt = $query->getStatement($this->conn);
        $stmt->execute();

        $rs = $this->db->select('test_db_table');
        $rs->rewind();
        $record = $rs->current();
        $this->assertEquals($record->get('title'), $title);
        $this->assertEquals($record->get('description'), $description);

        $rs->next();
        $record = $rs->current();
        $this->assertEquals($record->get('title'), $title);
        $this->assertEquals($record->get('description'), $description);
    }

    function testUpdateAddFieldWithoutValueOnlyReservesAPlaceholder()
    {
        $startId = $this->db->insert('test_db_table', array('description' => 'text1'));

        $query = new lmbUpdateQuery('test_db_table', $this->conn);
        $query->addField('description');
        $query->addField('title');

        $stmt = $query->getStatement($this->conn);
        $stmt->set('description', $description = 'Some \'description\'');
        $stmt->set('title', $title = 'Some title');
        $stmt->execute();

        $rs = $this->db->select('test_db_table');
        $rs->rewind();
        $record = $rs->current();
        $this->assertEquals($record->get('title'), $title);
        $this->assertEquals($record->get('description'), $description);
    }

//  function testUpdateSpecialCase()
//  {
//    $startId = $this->db->insert('test_db_table', array('description' => 'text1'));
//
//    $query = new lmbUpdateQuery('test_db_table', $this->conn);
//    $query->addRawField($this->conn->quoteIdentifier('id') . ' = ' . $this->conn->quoteIdentifier('id') . ' + 1');
//
//    $stmt = $query->getStatement($this->conn);
//    $stmt->execute();
//
//    $rs = $this->db->select('test_db_table');
//    $rs->rewind();
//    $record = $rs->current();
//    $this->assertEquals($record->get('id'), $startId + 1);
//  }

    function testUpdateWithCriteria()
    {
        $startId = $this->db->insert('test_db_table', array('description' => ''));
        $this->db->insert('test_db_table', array('description' => ''));

        $query = new lmbUpdateQuery('test_db_table', $this->conn);
        $query->addField('description', $description = 'Some description');
        $query->addField('title', $title = 'Some title');
        $query->addCriteria(new lmbSQLFieldCriteria('id', $startId + 1));

        $stmt = $query->getStatement($this->conn);
        $stmt->execute();

        $rs = $this->db->select('test_db_table');
        $rs->rewind();
        $record = $rs->current(); //this one is not changed
        $this->assertEquals($record->get('id'), $startId);
        $this->assertEquals($record->get('title'), '');
        $this->assertEquals($record->get('description'), '');

        $rs->next();
        $record = $rs->current();
        $this->assertEquals($record->get('id'), $startId + 1);
        $this->assertEquals($record->get('title'), $title);
        $this->assertEquals($record->get('description'), $description);
    }

    function testChaining()
    {
        $startId = $this->db->insert('test_db_table', array('description' => ''));
        $this->db->insert('test_db_table', array('description' => ''));

        $description = 'Some description';
        $title = 'Some title';

        $query = new lmbUpdateQuery('test_db_table', $this->conn);
        $query->set(array('description' => $description))->
        field('title', $title)->
        //rawField($this->conn->quoteIdentifier('id') . ' = ' . $this->conn->quoteIdentifier('id') . ' + 10')->
        where($this->conn->quoteIdentifier('id') . '=' . intval($startId + 1))->
        execute();

        $rs = $this->db->select('test_db_table');
        $rs->rewind();
        $record = $rs->current(); //this one is not changed
        $this->assertEquals($record->get('id'), $startId);
        $this->assertEquals($record->get('title'), '');
        $this->assertEquals($record->get('description'), '');

        $rs->next();
        $record = $rs->current();
        //$this->assertEquals($record->get('id'), $startId + 11);
        $this->assertEquals($record->get('title'), $title);
        $this->assertEquals($record->get('description'), $description);
    }
}
