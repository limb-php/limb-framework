<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\core\cases;

require_once ('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbArrayIterator;

class lmbArrayIteratorTest extends TestCase
{
  function testEmptyIterator()
  {
    $iterator = new lmbArrayIterator(array());
    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testIterate()
  {
    $data = array (array('x' => 1,'y' => 2),
                   array('x' => 3,'y' => 4),
                   array('x' => 5,'y' => 6));

    $iterator = new lmbArrayIterator(array('Ivan', 'Pavel', 'Serega'));
    $iterator->rewind();
    $this->assertTrue($iterator->valid());

    $this->assertEquals($iterator->current(), 'Ivan');
    $iterator->next();
    $this->assertTrue($iterator->valid());
    $this->assertEquals($iterator->current(), 'Pavel');
  }

  function testIterateOver()
  {
    $iterator = new lmbArrayIterator(array('Ivan'));
    $iterator->rewind();
    $iterator->next();
    $iterator->next();
    $this->assertFalse($iterator->valid());
    $this->assertNull($iterator->current());
  }
  
  function testIterateWithPagination()
  {
    $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
    $iterator->paginate($offset = 0, $limit = 2);
    $this->assertEquals($iterator->count(), 5);
    $this->assertEquals($iterator->countPaginated(), $limit);

    $iterator->rewind();
    $this->assertEquals($iterator->current(), 'a');
    $iterator->next();
    $this->assertEquals($iterator->current(), 'b');
  }

  function testIterateWithPaginationNonZeroOffset()
  {
    $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
    $iterator->paginate($offset = 2, $limit = 2);

    $iterator->rewind();
    $this->assertEquals($iterator->current(), 'c');
    $iterator->next();
    $this->assertEquals($iterator->current(), 'd');
  }

  function testPaginateWithOutOfBounds()
  {
    $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
    $iterator->paginate($offset = 5, $limit = 2);

    $this->assertEquals($iterator->count(), 5);
    $this->assertEquals($iterator->countPaginated(), 0);

    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testPaginateWithOffsetLessThanZero()
  {
    $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
    $iterator->paginate($offset = -1, $limit = 2);

    $this->assertEquals($iterator->count(), 5);
    $this->assertEquals($iterator->countPaginated(), 0);

    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }
}

