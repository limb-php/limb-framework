<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbArrayHelper;
use limb\core\src\lmbObject;

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
        $arr = [1, 'apple' => 2, 'basket' => array('chips' => 3, 'nachoes' => 4)];

        lmbArrayHelper::arrayMapRecursive(function ($v) {
            return $v + 1;
        }, $arr);

        $this->assertEquals(array(2, 'apple' => 3, 'basket' => array('chips' => 4, 'nachoes' => 5)), $arr);
    }

    function testSortArray()
    {
        $arr = [
            ['a' => 1, 'b' => 2],
            ['a' => 2, 'b' => 1],
            ['a' => 2, 'b' => 0]
        ];

        lmbArrayHelper::sortArray($arr, ['a' => 'DESC', 'b' => 'ASC']);
        $this->assertEquals([2 => ['a' => 2, 'b' => 0], 1 => ['a' => 2, 'b' => 1], 0 => ['a' => 1, 'b' => 2]], $arr);

        $arr = [
            ['a' => 1, 'b' => 2],
            ['a' => 2, 'b' => 1],
            ['a' => 2, 'b' => 0]
        ];

        lmbArrayHelper::sortArray($arr, ['a' => 'DESC', 'b' => 'ASC'], false);
        $this->assertEquals([['a' => 2, 'b' => 0], ['a' => 2, 'b' => 1], ['a' => 1, 'b' => 2]], $arr);
    }

    //
    // --- sort_params shape normalisation -----------------------------------
    //
    // Previously these loose forms would either silently do nothing or,
    // worse, call $row->get(0) on object rows and throw
    // lmbNoSuchPropertyException. Normalisation lives inside sortArray()
    // now, so every caller gets the same tolerant behaviour.
    //

    function testSortArrayIntKeyedList_ImpliesAsc()
    {
        // ['id'] means "sort ASC by id", NOT "sort by column '0'".
        $arr = [
            ['id' => 3, 'name' => 'c'],
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
        ];

        lmbArrayHelper::sortArray($arr, ['id'], false);

        $this->assertSame([
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
            ['id' => 3, 'name' => 'c'],
        ], $arr);
    }

    function testSortArrayStringParam_ImpliesAsc()
    {
        // Bare 'id' is equivalent to ['id' => 'ASC'].
        $arr = [['id' => 2], ['id' => 1], ['id' => 3]];

        lmbArrayHelper::sortArray($arr, 'id', false);

        $this->assertSame([['id' => 1], ['id' => 2], ['id' => 3]], $arr);
    }

    function testSortArrayMixedKeys_AreMergedCanonically()
    {
        // ['a', 'b' => 'DESC'] === ['a' => 'ASC', 'b' => 'DESC'].
        $arr = [
            ['a' => 1, 'b' => 1],
            ['a' => 1, 'b' => 3],
            ['a' => 1, 'b' => 2],
            ['a' => 2, 'b' => 9],
        ];

        lmbArrayHelper::sortArray($arr, ['a', 'b' => 'DESC'], false);

        $this->assertSame([
            ['a' => 1, 'b' => 3],
            ['a' => 1, 'b' => 2],
            ['a' => 1, 'b' => 1],
            ['a' => 2, 'b' => 9],
        ], $arr);
    }

    function testSortArrayIntKeyedList_OnObjectRows_DoesNotThrow()
    {
        // Regression: under the old code this path did $row->get(0) on an
        // lmbObject and tripped lmbNoSuchPropertyException. We assert both
        // no-throw and correct ordering here.
        $rows = [
            new lmbObject(['id' => 30, 'label' => 'c']),
            new lmbObject(['id' => 10, 'label' => 'a']),
            new lmbObject(['id' => 20, 'label' => 'b']),
        ];

        lmbArrayHelper::sortArray($rows, ['id'], false);

        $this->assertSame(10, $rows[0]->get('id'));
        $this->assertSame(20, $rows[1]->get('id'));
        $this->assertSame(30, $rows[2]->get('id'));
    }

    function testSortArrayEmptyParams_IsNoop()
    {
        // null / '' / [] all mean "don't sort". Keys should still be
        // preserved (default) and values must not reorder.
        foreach ([null, '', []] as $params) {
            $arr = ['a' => 2, 'b' => 1, 'c' => 3];
            $result = lmbArrayHelper::sortArray($arr, $params);
            $this->assertTrue($result);
            $this->assertSame(['a' => 2, 'b' => 1, 'c' => 3], $arr,
                'sortArray must not reorder when params are ' . var_export($params, true));
        }
    }

    function testSortArrayNonStringValueInIntKey_IsDropped()
    {
        // Garbage like [0 => null, 1 => 123] shouldn't crash or invent
        // column names — the normaliser silently drops those entries.
        $arr = [
            ['id' => 2, 'name' => 'b'],
            ['id' => 1, 'name' => 'a'],
        ];

        lmbArrayHelper::sortArray($arr, [null, 123, 'id'], false);

        $this->assertSame([
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
        ], $arr);
    }
}
