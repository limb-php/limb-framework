<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\dbal\cases\driver;

use limb\dbal\src\drivers\lmbDbConnectionInterface;
use PHPUnit\Framework\TestCase;

abstract class DriverRecordSetTestBase extends TestCase
{
    protected $record_class;

    /** @var lmbDbConnectionInterface $connection */
    protected $connection;

    function init($record_class)
    {
        $this->record_class = $record_class;
    }

    function setUp(): void
    {
        parent::setUp();

        $sql = "SELECT id, first FROM founding_fathers ORDER BY id";
        $this->stmt = $this->connection->newStatement($sql);
        $this->cursor = $this->stmt->getRecordSet();
    }

    function tearDown(): void
    {
        parent::tearDown();

        $this->connection->disconnect();

        lmb_tests_teardown_db();
    }

    function testRewind()
    {
        $this->cursor->rewind();
        $this->assertTrue($this->cursor->valid());
        $record = $this->cursor->current();
        $this->assertInstanceOf($this->record_class, $record);
        $this->assertEquals(10, $record->get('id'));
        $this->assertEquals('George', $record->get('first'));
        $this->cursor->next();
        $this->cursor->next();
        $this->cursor->rewind();
        $record = $this->cursor->current();
        $this->assertInstanceOf($this->record_class, $record);
        $this->assertEquals(10, $record->get('id'));
        $this->assertEquals('George', $record->get('first'));
    }

    function testIteration()
    {
        for ($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++) {
            $record = $this->cursor->current();
            $this->assertInstanceOf($this->record_class, $record);
        }
        $this->assertEquals(3, $i);
    }

    function testIteratorInterface()
    {
        $i = 0;
        foreach ($this->cursor as $record) {
            $this->assertInstanceOf($this->record_class, $record);
            $i++;
        }
        $this->assertEquals(3, $i);
    }

    function testPagerIteration()
    {
        $this->cursor->paginate($offset = 0, $limit = 2);
        for ($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++) ;
        $this->assertEquals(2, $i);
    }

    function testPaginationAfterIterating()
    {
        for ($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++) ;
        $this->assertEquals(3, $i);
        $this->cursor->paginate($offset = 0, $limit = 2);
        for ($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++) ;
        $this->assertEquals(2, $i);
    }

    function testPagerIterationPassingStringInsteadOfNumber()
    {
        $this->cursor->paginate($offset = ';Select * FROM some_table', $limit = 2);
        for ($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++) ;
        $this->assertEquals(2, $i);
    }

    function testCount()
    {
        $sql = "SELECT * FROM founding_fathers";
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->paginate(0, 2);

        $this->assertEquals(3, $rs->count());
        $this->assertEquals(2, $rs->countPaginated());
        //double test driver internal state
        $this->assertEquals(3, $rs->count());
        $this->assertEquals(2, $rs->countPaginated());
    }

    function testSort()
    {
        $sql = "SELECT id, first FROM founding_fathers";
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->sort(array('id' => 'DESC'));

        $rs->rewind();
        $this->assertEquals('Benjamin', $rs->current()->get('first'));
        $rs->next();
        $this->assertEquals('Alexander', $rs->current()->get('first'));
        $rs->next();
        $this->assertEquals('George', $rs->current()->get('first'));
    }

    function testSortPaginated()
    {
        $sql = "SELECT id, first FROM founding_fathers";
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->sort(array('id' => 'DESC'));
        $rs->paginate(0, 1);

        $rs->rewind();
        $this->assertEquals('Benjamin', $rs->current()->get('first'));
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testSortPreservesExistingOrderBy()
    {
        $sql = "SELECT id, first FROM founding_fathers ORdeR By first";
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->sort(array('id' => 'DESC'));

        $rs->rewind();
        $this->assertEquals('Alexander', $rs->current()->get('first'));
        $rs->next();
        $this->assertEquals('Benjamin', $rs->current()->get('first'));
        $rs->next();
        $this->assertEquals('George', $rs->current()->get('first'));
    }

    function testAt()
    {
        $this->assertEquals('Alexander', $this->cursor->at(1)->get('first'));
        $this->assertEquals('George', $this->cursor->at(0)->get('first'));
        $this->assertNull($this->cursor->at(100));
    }

    function testsAtAfterPagination()
    {
        $sql = "SELECT id, first FROM founding_fathers";
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->paginate(1, 1);

        $this->assertEquals('George', $rs->at(0)->get('first'));
    }

    function testsAtAfterSort()
    {
        $sql = "SELECT id, first FROM founding_fathers";
        $rs = $this->connection->newStatement($sql)->getRecordSet();
        $rs->sort(array('id' => 'DESC'));

        $this->assertEquals('Benjamin', $rs->at(0)->get('first'));
        $this->assertEquals('Alexander', $rs->at(1)->get('first'));
        $this->assertEquals('George', $rs->at(2)->get('first'));
    }

    function testGetFlatArray()
    {
        $sql = "SELECT first FROM founding_fathers";
        $rs = $this->connection->newStatement($sql)->getRecordSet();

        $flat_array = array(
            array('first' => 'George'),
            array('first' => 'Alexander'),
            array('first' => 'Benjamin'),
        );

        $this->assertEquals($flat_array, $rs->getFlatArray());
    }
}
