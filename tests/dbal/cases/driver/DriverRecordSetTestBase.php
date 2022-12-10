<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\driver;
use PHPUnit\Framework\TestCase;

abstract class DriverRecordSetTestBase extends TestCase
{
  var $record_class;

  function DriverRecordSetTestBase($record_class)
  {
    $this->record_class = $record_class;
  }

  function setUp(): void
  {
    $sql = "SELECT id, first FROM founding_fathers ORDER BY id";
    $this->stmt = $this->connection->newStatement($sql);
    $this->cursor = $this->stmt->getRecordSet();
  }

  function tearDown(): void
  {
    $this->connection->disconnect();
  }

  function testRewind()
  {
    $this->cursor->rewind();
    $this->assertTrue($this->cursor->valid());
    $record = $this->cursor->current();
    $this->assertIsA($record, $this->record_class);
    $this->assertEquals($record->get('id'), 1);
    $this->assertEquals($record->get('first'), 'George');
    $this->cursor->next();
    $this->cursor->next();
    $this->cursor->rewind();
    $record = $this->cursor->current();
    $this->assertIsA($record, $this->record_class);
    $this->assertEquals($record->get('id'), 1);
    $this->assertEquals($record->get('first'), 'George');
  }

  function testIteration()
  {
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++)
    {
      $record = $this->cursor->current();
      $this->assertIsA($record, $this->record_class);
    }
    $this->assertEquals($i, 3);
  }

  function testIteratorInterface()
  {
    $i = 0;
    foreach($this->cursor as $record)
    {
      $this->assertIsA($record, $this->record_class);
      $i++;
    }
    $this->assertEquals($i, 3);
  }

  function testPagerIteration()
  {
    $this->cursor->paginate($offset = 0, $limit = 2);
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEquals($i, 2);
  }

  function testPaganationAfterIterating()
  {
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEquals($i, 3);
    $this->cursor->paginate($offset = 0, $limit = 2);
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEquals($i, 2);
  }

  function testPagerIterationPassingStringInsteadOfNumber()
  {
    $this->cursor->paginate($offset = ';Select * FROM some_table', $limit = 2);
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEquals($i, 2);
  }

  function testCount()
  {
    $sql = "SELECT * FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->paginate(0, 2);

    $this->assertEquals($rs->count(), 3);
    $this->assertEquals($rs->countPaginated(), 2);
    //double test driver internal state
    $this->assertEquals($rs->count(), 3);
    $this->assertEquals($rs->countPaginated(), 2);
  }

  function testSort()
  {
    $sql = "SELECT id, first FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $rs->rewind();
    $this->assertEquals($rs->current()->get('first'), 'Benjamin');
    $rs->next();
    $this->assertEquals($rs->current()->get('first'), 'Alexander');
    $rs->next();
    $this->assertEquals($rs->current()->get('first'), 'George');
  }

  function testSortPaginated()
  {
    $sql = "SELECT id, first FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));
    $rs->paginate(0, 1);

    $rs->rewind();
    $this->assertEquals($rs->current()->get('first'), 'Benjamin');
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testSortPreservesExistingOrderBy()
  {
    $sql = "SELECT id, first FROM founding_fathers ORdeR By first";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $rs->rewind();
    $this->assertEquals($rs->current()->get('first'), 'Alexander');
    $rs->next();
    $this->assertEquals($rs->current()->get('first'), 'Benjamin');
    $rs->next();
    $this->assertEquals($rs->current()->get('first'), 'George');
  }

  function testAt()
  {
    $this->assertEquals($this->cursor->at(1)->get('first'), 'Alexander');
    $this->assertEquals($this->cursor->at(0)->get('first'), 'George');
    $this->assertNull($this->cursor->at(100));
  }

  function testsAtAfterPagination()
  {
    $sql = "SELECT id, first FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->paginate(1, 1);

    $this->assertEquals($rs->at(0)->get('first'), 'George');
  }

  function testsAtAfterSort()
  {
    $sql = "SELECT id, first FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $this->assertEquals($rs->at(0)->get('first'), 'Benjamin');
    $this->assertEquals($rs->at(1)->get('first'), 'Alexander');
    $this->assertEquals($rs->at(2)->get('first'), 'George');
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

    $this->assertIdentical($flat_array, $rs->getFlatArray());
  }
}
