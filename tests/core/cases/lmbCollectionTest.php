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
    $data = array (array('x' => 1,'y' => 2),
                   array('x' => 3,'y' => 4),
                   array('x' => 5,'y' => 6));

    $iterator = new lmbCollection($data);
    $iterator->rewind();
    $this->assertTrue($iterator->valid());

    $dataspace1 = $iterator->current();
    $this->assertEquals($dataspace1->export(), array('x' => 1,'y' => 2));

    $iterator->next();
    $dataspace2 = $iterator->current();
    $this->assertEquals($dataspace2->export(), array('x' => 3,'y' => 4));
  }

  function testIterateOver()
  {
    $data = array (array('x' => 1,'y' => 2),
                   array('x' => 3,'y' => 4));
    $iterator = new lmbCollection($data);
    $iterator->rewind();
    $iterator->next();
    $iterator->next();
    $this->assertFalse($iterator->valid());
    $dataspace = $iterator->current();
    $this->assertEquals($dataspace->export(), array());
  }

  function testIterateWithForeach()
  {
    $data = array (array('x' => '1'),
                   array('x' => '2'),
                   array('x' => '3'));

    $iterator = new lmbCollection($data);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEquals($str, '123');
  }

  function testWorksOkWithArrayOfSets()
  {
    $data = array(new lmbSet(array('x' => '1')),
                  new lmbSet(array('x' => '2')),
                  new lmbSet(array('x' => '3')));

    $iterator = new lmbCollection($data);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEquals($str, '123');
  }

  function testAdd()
  {
    $item1 = new lmbSet(array('x' => 1,'y' => 2));
    $item2 = new lmbSet(array('x' => 3,'y' => 4));

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
    $data = array (array('x' => 'C'),
                   array('x' => 'A'),
                   array('x' => 'B'));

    $iterator = new lmbCollection($data);
    $iterator->sort(array('x' => 'DESC'));
    $arr = $iterator->getArray();
    $this->assertEquals($arr[0]['x'], 'C');
    $this->assertEquals($arr[1]['x'], 'B');
    $this->assertEquals($arr[2]['x'], 'A');
  }

  function testSortWorksOkWithSetsToo()
  {
    $item1 = new lmbSet(array('x' => 'C'));
    $item2 = new lmbSet(array('x' => 'A'));
    $item3 = new lmbSet(array('x' => 'B'));

    $iterator = new lmbCollection(array($item1, $item2, $item3));
    $iterator->sort(array('x' => 'DESC'));
    $arr = $iterator->getArray();
    $this->assertEquals($arr[0]->get('x'), 'C');
    $this->assertEquals($arr[1]->get('x'), 'B');
    $this->assertEquals($arr[2]->get('x'), 'A');
  }

  function testDontSortEmptyCollection()
  {
    $iterator = new lmbCollection();
    $iterator->sort(array('x' => 'DESC'));
    $this->assertEquals($iterator->getArray(), array());
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

    $this->assertEquals(lmbCollection :: concat($col1, $col2, $col3),
                       new lmbCollection(array($item1, $item2, $item3, $item4)));
  }

  function testToFlatArrayWithKeyField()
  {
    $data = array (array('x' => 'C'),
                   array('x' => 'A'),
                   array('x' => 'B'));

    $iterator = new lmbCollection($data);

    $arr = lmbCollection :: toFlatArray($iterator, 'x');
    $this->assertTrue(isset($arr['A']));
    $this->assertEquals($arr['A'], array('x' => 'A'));

    $this->assertTrue(isset($arr['B']));
    $this->assertEquals($arr['B'], array('x' => 'B'));

    $this->assertTrue(isset($arr['C']));
    $this->assertEquals($arr['C'], array('x' => 'C'));
  }
}

