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
use limb\core\src\lmbArrayHelper;

class lmbArrayHelperTest extends TestCase
{
    public function testArrayMerge()
    {
        $a = array('orange', 'nested' => array(1), 'b' => 1);
        $b = array('apple', 'nested' => array(2), 'a' => 1);

        $this->assertEquals(array('apple', 'nested' => array(2), 'b' => 1, 'a' => 1),
            lmbArrayHelper::arrayMerge($a, $b));
    }

    public function testArrayMergeMany()
    {
        $a = array('orange', 'nested' => array(1), 'b' => 1);
        $b = array('apple', 'nested' => array(2), 'a' => 1);
        $c = array('banana', 'b' => 2);

        $this->assertEquals(array('banana', 'nested' => array(2), 'b' => 2, 'a' => 1),
            lmbArrayHelper::arrayMerge($a, $b, $c));

    }

    function testMap()
    {
        $map = array('foo' => 'foo1', 'bar' => 'bar1');
        $src = array('foo' => 1, 'bar' => 2);
        $dest = array();

        lmbArrayHelper::Map($map, $src, $dest);

        $this->assertEquals(array('foo1' => 1, 'bar1' => 2), $dest);
    }

    function testExplode()
    {
        $string = 'man:bob,dog:willy';
        $res = lmbArrayHelper::explode(',', ':', $string);
        $this->assertEquals(array('man' => 'bob', 'dog' => 'willy'), $res);
    }

    function testGetColumnValues()
    {
        $arr = array(array('foo' => 1), array('foo' => 2));

        $this->assertEquals(array(1, 2), lmbArrayHelper::getColumnValues('foo', $arr));
    }

    function testGetMaxColumnValue()
    {
        $arr = array(array('foo' => 1), array('foo' => 2));

        $this->assertEquals(2, lmbArrayHelper::getMaxColumnValue('foo', $arr, $pos));
        $this->assertEquals(1, $pos);
    }

    function testGetMinColumnValue()
    {
        $arr = array(array('foo' => 1), array('foo' => 2));

        $this->assertEquals(1, lmbArrayHelper::getMinColumnValue('foo', $arr, $pos));
        $this->assertEquals(0, $pos);
    }

    function testToFlatArray()
    {
        $arr = array(1, 'apple' => 2, 'basket' => array('chips' => 3, 'nachoes' => 4));

        lmbArrayHelper::toFlatArray($arr, $result1);
        $this->assertEquals(array(1, 'apple' => 2, 'basket[chips]' => 3, 'basket[nachoes]' => 4), $result1);

        lmbArrayHelper::toFlatArray($arr, $result2, '_');
        $this->assertEquals(array('_[0]' => 1, '_[apple]' => 2, '_[basket][chips]' => 3, '_[basket][nachoes]' => 4), $result2);
    }

    function testArrayMapRecursive()
    {
        $arr = array(1, 'apple' => 2, 'basket' => array('chips' => 3, 'nachoes' => 4));

        lmbArrayHelper::arrayMapRecursive(function ($v) {
            return $v + 1;
        }, $arr);

        $this->assertEquals(array(2, 'apple' => 3, 'basket' => array('chips' => 4, 'nachoes' => 5)), $arr);
    }

    function testSortArray()
    {
        $arr = array(array('a' => 1, 'b' => 2), array('a' => 2, 'b' => 1), array('a' => 2, 'b' => 0));

        $res = lmbArrayHelper::sortArray($arr, array('a' => 'DESC', 'b' => 'ASC'));
        $this->assertEquals(array(2 => array('a' => 2, 'b' => 0), 1 => array('a' => 2, 'b' => 1), 0 => array('a' => 1, 'b' => 2)), $res);

        $res = lmbArrayHelper::sortArray($arr, array('a' => 'DESC', 'b' => 'ASC'), false);
        $this->assertEquals(array(array('a' => 2, 'b' => 0), array('a' => 2, 'b' => 1), array('a' => 1, 'b' => 2)), $res);
    }
}
