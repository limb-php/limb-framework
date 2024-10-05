<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace Limb\Tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\lmbCollection;
use limb\core\lmbSet;

class lmbCollectionPaginationTest extends TestCase
{
    function testIterateWithPagination()
    {
        $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
        $iterator = new lmbCollection($data);
        $iterator->paginate($offset = 0, $limit = 2);
        $this->assertEquals(5, $iterator->count());
        $this->assertEquals($iterator->countPaginated(), $limit);

        $iterator->rewind();
        $dataspace1 = $iterator->current();
        $this->assertEquals(array('x' => 'a'), $dataspace1->export());
        $iterator->next();
        $dataspace2 = $iterator->current();
        $this->assertEquals(array('x' => 'b'), $dataspace2->export());
    }

    function testIterateWithPaginationNonZeroOffset()
    {
        $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
        $iterator = new lmbCollection($data);
        $iterator->paginate($offset = 2, $limit = 2);

        $iterator->rewind();
        $dataspace1 = $iterator->current();
        $this->assertEquals(array('x' => 'c'), $dataspace1->export());
        $iterator->next();
        $dataspace2 = $iterator->current();
        $this->assertEquals(array('x' => 'd'), $dataspace2->export());
    }

    function testPaginateWithOutOfBounds()
    {
        $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
        $iterator = new lmbCollection($data);
        $iterator->paginate($offset = 5, $limit = 2);

        $this->assertEquals(5, $iterator->count());
        $this->assertEquals(0, $iterator->countPaginated());

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    function testPaginateWithOffsetLessThanZero()
    {
        $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
        $iterator = new lmbCollection($data);
        $iterator->paginate($offset = -1, $limit = 2);

        $this->assertEquals(5, $iterator->count());
        $this->assertEquals(0, $iterator->countPaginated());

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    function testWorksOkWithArrayOfSets()
    {
        $data = array(new lmbSet(array('x' => '1')),
            new lmbSet(array('x' => '2')),
            new lmbSet(array('x' => '3')));

        $iterator = new lmbCollection($data);
        $iterator->paginate($offset = 1, $limit = 2);

        $str = '';
        foreach ($iterator as $record)
            $str .= $record->get('x');

        $this->assertEquals('23', $str);
    }

    function testResetInternalIteratorIfPrimaryDatasetChanged()
    {
        $data = array(new lmbSet(array('x' => '1')),
            new lmbSet(array('x' => '2')),
            new lmbSet(array('x' => '3')));

        $iterator = new lmbCollection($data);
        $iterator->paginate($offset = 1, $limit = 3);

        $str = '';
        foreach ($iterator as $record)
            $str .= $record->get('x');

        $this->assertEquals('23', $str);

        $iterator->add(new lmbSet(array('x' => '4')));

        $str = '';
        foreach ($iterator as $record)
            $str .= $record->get('x');

        $this->assertEquals('234', $str);
    }

    function testResetInternalIteratorOnSortToo()
    {
        $data = array(new lmbSet(array('x' => 'C')),
            new lmbSet(array('x' => 'A')),
            new lmbSet(array('x' => 'B')));

        $iterator = new lmbCollection($data);
        $iterator->paginate($offset = 1, $limit = 2);

        $str = '';
        foreach ($iterator as $record)
            $str .= $record->get('x');

        $this->assertEquals('AB', $str);

        $iterator->sort(array('x' => 'DESC'));

        $str = '';
        foreach ($iterator as $record)
            $str .= $record->get('x');

        $this->assertEquals('BA', $str);
    }

}
