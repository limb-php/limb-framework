<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\Core\Cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\lmbArrayIterator;

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
        $data = array(array('x' => 1, 'y' => 2),
            array('x' => 3, 'y' => 4),
            array('x' => 5, 'y' => 6));

        $iterator = new lmbArrayIterator(array('Ivan', 'Pavel', 'Serega'));
        $iterator->rewind();

        $this->assertTrue($iterator->valid());

        $this->assertEquals('Ivan', $iterator->current());

        $iterator->next();

        $this->assertTrue($iterator->valid());
        $this->assertEquals('Pavel', $iterator->current());
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

        $this->assertEquals(5, $iterator->count());
        $this->assertEquals($limit, $iterator->countPaginated());

        $iterator->rewind();

        $this->assertEquals('a', $iterator->current());

        $iterator->next();

        $this->assertEquals('b', $iterator->current());
    }

    function testIterateWithPaginationNonZeroOffset()
    {
        $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
        $iterator->paginate($offset = 2, $limit = 2);

        $iterator->rewind();

        $this->assertEquals('c', $iterator->current());

        $iterator->next();

        $this->assertEquals('d', $iterator->current());
    }

    function testPaginateWithOutOfBounds()
    {
        $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
        $iterator->paginate($offset = 5, $limit = 2);

        $this->assertEquals(5, $iterator->count());
        $this->assertEquals(0, $iterator->countPaginated());

        $iterator->rewind();

        $this->assertFalse($iterator->valid());
    }

    function testPaginateWithOffsetLessThanZero()
    {
        $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
        $iterator->paginate($offset = -1, $limit = 2);

        $this->assertEquals(5, $iterator->count());
        $this->assertEquals(0, $iterator->countPaginated());

        $iterator->rewind();

        $this->assertFalse($iterator->valid());
    }

    function testPaginateGetPartOfIterator()
    {
        $iterator = new lmbArrayIterator(array('a', 'b', 'c', 'd', 'e'));
        $iterator->paginate($offset = 2, $limit = 2);

        $result = [];
        foreach ($iterator as $item) {
            $result[] = $item;
        }

        $this->assertEquals(['c', 'd'], $result);
    }

    function testIteratorToJson()
    {
        $iterator = new lmbArrayIterator(array("C", "A", "B"));

        $json_str = json_encode($iterator);
        $json_arr = json_decode($json_str, true);

        $this->assertEquals('C', $json_arr[0]);
        $this->assertEquals('A', $json_arr[1]);
        $this->assertEquals('B', $json_arr[2]);
    }
}
