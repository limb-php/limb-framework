<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbCollection;
use limb\core\src\lmbSet;

class lmbCollectionTest extends TestCase
{
    function testEmptyIterator()
    {
        $iterator = new lmbCollection(array());
        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    function testIterateArrayWithFalseValue()
    {
        $iterator = new lmbCollection(array(false));
        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    function testIterate()
    {
        $data = array(array('x' => 1, 'y' => 2),
            array('x' => 3, 'y' => 4),
            array('x' => 5, 'y' => 6));

        $iterator = new lmbCollection($data);
        $iterator->rewind();
        $this->assertTrue($iterator->valid());

        $dataspace1 = $iterator->current();
        $this->assertEquals(array('x' => 1, 'y' => 2), $dataspace1->export());

        $iterator->next();
        $dataspace2 = $iterator->current();
        $this->assertEquals(array('x' => 3, 'y' => 4), $dataspace2->export());
    }

    function testIterateOver()
    {
        $data = array(array('x' => 1, 'y' => 2),
            array('x' => 3, 'y' => 4));
        $iterator = new lmbCollection($data);
        $iterator->rewind();
        $iterator->next();
        $iterator->next();
        $this->assertFalse($iterator->valid());
        $dataspace = $iterator->current();
        $this->assertEquals(array(), $dataspace->export());
    }

    function testIterateWithForeach()
    {
        $data = array(array('x' => '1'),
            array('x' => '2'),
            array('x' => '3'));

        $iterator = new lmbCollection($data);

        $str = '';
        foreach ($iterator as $record)
            $str .= $record->get('x');

        $this->assertEquals('123', $str);
    }

    function testWorksOkWithArrayOfSets()
    {
        $data = array(new lmbSet(array('x' => '1')),
            new lmbSet(array('x' => '2')),
            new lmbSet(array('x' => '3')));

        $iterator = new lmbCollection($data);

        $str = '';
        foreach ($iterator as $record)
            $str .= $record->get('x');

        $this->assertEquals('123', $str);
    }

    function testAdd()
    {
        $item1 = new lmbSet(array('x' => 1, 'y' => 2));
        $item2 = new lmbSet(array('x' => 3, 'y' => 4));

        $iterator = new lmbCollection();
        $this->assertTrue($iterator->isEmpty());
        $iterator->add($item1);
        $this->assertFalse($iterator->isEmpty());
        $iterator->add($item2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());

        $this->assertEquals($iterator->current(), $item1);
        $iterator->next();
        $this->assertEquals($iterator->current(), $item2);
    }

    function testAddToPositionAndSortByKeys()
    {
        $item1 = new lmbSet(array(1));
        $item2 = new lmbSet(array(2));

        $iterator = new lmbCollection(array(), $sort_items_by_key = true);
        $this->assertTrue($iterator->isEmpty());
        $iterator->add($item1, 1);
        $this->assertFalse($iterator->isEmpty());
        $iterator->add($item2, 0);

        $iterator->sortByKeys();

        $iterator->rewind();

        $this->assertTrue($iterator->valid());

        $this->assertEquals($iterator->current(), $item2);
        $iterator->next();
        $this->assertEquals($iterator->current(), $item1);
    }

    function testSort()
    {
        $data = array(array('x' => 'C'),
            array('x' => 'A'),
            array('x' => 'B'));

        $iterator = new lmbCollection($data);
        $iterator->sort(array('x' => 'DESC'));
        $arr = $iterator->getArray();
        $this->assertEquals('C', $arr[0]['x']);
        $this->assertEquals('B', $arr[1]['x']);
        $this->assertEquals('A', $arr[2]['x']);
    }

    function testSortWorksOkWithSetsToo()
    {
        $item1 = new lmbSet(array('x' => 'C'));
        $item2 = new lmbSet(array('x' => 'A'));
        $item3 = new lmbSet(array('x' => 'B'));

        $iterator = new lmbCollection(array($item1, $item2, $item3));
        $iterator->sort(array('x' => 'DESC'));
        $arr = $iterator->getArray();
        $this->assertEquals('C', $arr[0]->get('x'));
        $this->assertEquals('B', $arr[1]->get('x'));
        $this->assertEquals('A', $arr[2]->get('x'));
    }

    function testDontSortEmptyCollection()
    {
        $iterator = new lmbCollection();
        $iterator->sort(array('x' => 'DESC'));
        $this->assertEquals(array(), $iterator->getArray());
    }

    function testConcat()
    {
        $item1 = new lmbSet(array('x' => 'C'));
        $item2 = new lmbSet(array('x' => 'A'));
        $item3 = new lmbSet(array('x' => 'B'));
        $item4 = new lmbSet(array('x' => 'D'));

        $col1 = new lmbCollection(array($item1, $item2));
        $col2 = new lmbCollection(array($item3));
        $col3 = new lmbCollection(array($item4));

        $this->assertEquals(lmbCollection::concat($col1, $col2, $col3),
            new lmbCollection(array($item1, $item2, $item3, $item4)));
    }

    function testToFlatArrayWithKeyField()
    {
        $data = array(array('x' => 'C'),
            array('x' => 'A'),
            array('x' => 'B'));

        $iterator = new lmbCollection($data);

        $arr = lmbCollection::toFlatArray($iterator, 'x');
        $this->assertTrue(isset($arr['A']));
        $this->assertEquals(array('x' => 'A'), $arr['A']);

        $this->assertTrue(isset($arr['B']));
        $this->assertEquals(array('x' => 'B'), $arr['B']);

        $this->assertTrue(isset($arr['C']));
        $this->assertEquals(array('x' => 'C'), $arr['C']);
    }

    function testCollectionToJson()
    {
        $item1 = new lmbSet(array('x' => 'C'));
        $item2 = new lmbSet(array('x' => 'A'));
        $item3 = new lmbSet(array('x' => 'B'));

        $iterator = new lmbCollection(array($item1, $item2, $item3));

        $json_str = json_encode($iterator);
        $json_arr = json_decode($json_str, true);

        $this->assertEquals('C', $json_arr[0]['x']);
        $this->assertEquals('A', $json_arr[1]['x']);
        $this->assertEquals('B', $json_arr[2]['x']);
    }
}
