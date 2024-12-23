<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\ActiveRecord\Cases;

use Limb\Tests\ActiveRecord\Cases\src\TestAutoTimesObject;

class lmbARAutoTimesTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('test_auto_times_object');

    function testSetTimesAutomaticallyOnCreate()
    {
        $time = time();
        $object = new TestAutoTimesObject();
        $object->setContent('whatever');

        $id = $object->save();

        $object2 = new TestAutoTimesObject($id);
        $this->assertTrue($object2->getUpdateTime() >= $time);
        $this->assertTrue($object2->getCreateTime() >= $time);

        $this->assertEquals($object->getUpdateTime(), $object2->getUpdateTime());
        $this->assertEquals($object->getCreateTime(), $object2->getCreateTime());
    }

    function testSetTimesAutomaticallyOnUpdate()
    {
        $time = time();
        $object = new TestAutoTimesObject();
        $object->setContent('whatever');

        $object->save();
        $ctime1 = $object->getCreateTime();
        $utime1 = $object->getUpdateTime();

        sleep(1);

        $object->setContent('whatever2');//without this object is considered to be not dirty
        $object->save();
        $ctime2 = $object->getCreateTime();
        $utime2 = $object->getUpdateTime();

        $this->assertTrue($ctime1 >= $time);
        $this->assertTrue($utime1 >= $time);
        $this->assertEquals($ctime1, $ctime2);
        $this->assertTrue($utime2 > $utime1);

        $object2 = new TestAutoTimesObject($object->getId());
        $this->assertEquals($object2->getCreateTime(), $ctime1);
        $this->assertEquals($object2->getUpdateTime(), $utime2);
    }
}
