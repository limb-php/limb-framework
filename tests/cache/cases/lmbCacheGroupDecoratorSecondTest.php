<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

require_once (dirname(__FILE__) . '/init.inc.php');

use limb\core\src\lmbEnv;
use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbToolkit;
use limb\cache\src\lmbCacheGroupDecorator;
use limb\cache\src\lmbCacheFileBackend;
use tests\cache\cases\src\SomeClass;

class lmbCacheGroupDecoratorSecondTest extends TestCase
{
    function _createBackend()
    {
        $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
        return new lmbCacheFileBackend($this->cache_dir);
    }

    function _createPersisterImp()
    {
        return new lmbCacheGroupDecorator($this->_createBackend(), 'default_group');
    }

    function testPutAndReadWithOutGroup()
    {
        lmbToolkit::instance()->setCache($this->_createBackend());
        lmbToolkit::instance()->getCache()->flush();

        $a = SomeClass::Foo('a');
        $b = SomeClass::Foo('b');

        $cache1 = lmbToolkit::instance()->getCache();

        $a_read = $cache1->get('bara', array('group' => 'bar_group'));
        $b_read = $cache1->get('barb', array('group' => 'bar_group'));

        $this->assertEquals($a_read, $a);
        $this->assertEquals($b_read, $b);
    }


    function testPutAndReadWithGroup()
    {

        lmbToolkit::instance()->setCache($this->_createPersisterImp());
        lmbToolkit::instance()->getCache()->flush();

        $a = SomeClass::Foo('a');
        $b = SomeClass::Foo('b');

        $cache1 = lmbToolkit::instance()->getCache();

        $a_read = $cache1->get('bara', array('group' => 'bar_group'));
        $b_read = $cache1->get('barb', array('group' => 'bar_group'));

        $this->assertEquals($a_read, $a);
        $this->assertEquals($b_read, $b);

        $a = SomeClass::Foo('a');
        $b = SomeClass::Foo('b');
    }

    function testManyPutAndReadWithGroupAndDestroyTolkitObjectCache()
    {

        lmbToolkit::instance()->setCache($this->_createPersisterImp());
        lmbToolkit::instance()->getCache()->flush();

        $a = SomeClass::Foo('a');
        $b = SomeClass::Foo('b');

        $cache1 = lmbToolkit::instance()->getCache();

        $a_read = $cache1->get('bara', array('group' => 'bar_group'));
        $b_read = $cache1->get('barb', array('group' => 'bar_group'));

        $this->assertEquals($a_read, $a);
        $this->assertEquals($b_read, $b);

        $a = SomeClass::Foo('a');
        $b = SomeClass::Foo('b');

        //This need to destroy cache in toolkit
        lmbToolkit::instance()->setCache($this->_createPersisterImp());


        $cache1 = lmbToolkit::instance()->getCache();

        $a_read = $cache1->get('bara', array('group' => 'bar_group'));
        $b_read = $cache1->get('barb', array('group' => 'bar_group'));

        $this->assertEquals($a_read, $a);
        $this->assertEquals($b_read, $b);

    }
}
