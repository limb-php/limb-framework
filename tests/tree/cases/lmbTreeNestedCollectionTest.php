<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\tree\cases;

use PHPUnit\Framework\TestCase;
use limb\tree\src\lmbTreeNestedCollection;

require_once('.setup.php');

class lmbTreeNestedCollectionTest extends TestCase
{
  function testMakeNestedOneElementRs()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
      );

    $expected_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
      );

    $raw = new \ArrayIterator($raw_tree_array);
    $nested = new lmbTreeNestedCollection($raw);
    $arr = $this->toArray($nested);

    $this->assertEquals($arr, $expected_tree_array);
  }

  function testMakeNestedSimpleRs()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
        array('id' => 2, 'parent_id' => 1),
          array('id' => 5, 'parent_id' => 2),
        array('id' => 3, 'parent_id' => 1),
      array('id' => 4, 'parent_id' => 100),
      );

    $expected_tree_array = array(
      array('id' => 1, 'parent_id' => 0, 
            'children' => array(array('id' => 2, 'parent_id' => 1, 
                                      'children' => array(array('id' => 5, 'parent_id' => 2))),
                          array('id' => 3, 'parent_id' => 1))),
      array('id' => 4, 'parent_id' => 100)
      );

    $raw = new \ArrayIterator($raw_tree_array);
    $nested = new lmbTreeNestedCollection($raw);
    $arr = $this->toArray($nested);

    $this->assertEquals($arr, $expected_tree_array);
  }

  function testMakeNestedMoreComplexRs()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
        array('id' => 2, 'parent_id' => 1),
          array('id' => 3, 'parent_id' => 2),
          array('id' => 4, 'parent_id' => 2),
        array('id' => 5, 'parent_id' => 1),
      array('id' => 6, 'parent_id' => 100),
        array('id' => 7, 'parent_id' => 6),
      array('id' => 8, 'parent_id' => 200),
    );

    $expected_tree_array = array(
      array('id' => 1,
            'parent_id' => 0,
            'children' =>  array(
               array('id' => 2,
                    'parent_id' => 1,
                    'children' => array(
                        array('id' => 3, 'parent_id' => 2),
                        array('id' => 4, 'parent_id' => 2),
                     )
               ),
               array('id' => 5,
                    'parent_id' => 1
               )
            )
      ),
      array('id' => 6,
            'parent_id' => 100,
            'children' => array(
                array('id' => 7, 'parent_id' => 6),
             )
      ),
      array('id' => 8, 'parent_id' => 200),
    );

    $raw = new \ArrayIterator($raw_tree_array);
    $nested = new lmbTreeNestedCollection($raw);
    $arr = $this->toArray($nested);

    $this->assertEquals($arr, $expected_tree_array);
  }

  function toArray($iterator)
  {
    $result = array();
    foreach($iterator as $record)
      $result[] = $record->export();
    return $result;
  }
}
