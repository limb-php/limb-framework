<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\cache\cases;

require ('.setup.php');

use limb\core\src\lmbEnv;
use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbToolkit;
use limb\cache\src\lmbCacheGroupDecorator;
use limb\cache\src\lmbCacheFileBackend;

class SomeClass
{
  static function Foo($param)
  {
    $cache = lmbToolkit::instance()->getCache();
    if($value=$cache->get('bar'.$param,array('group'=>'bar_group')))
    {
      return $value;
    }

    $value='bar_value_'.$param;

    $cache->set('bar'.$param,$value,array('group'=>'bar_group'));

    return $value;
  }
}


class lmbCacheGroupDecoratorSecondTest extends TestCase
{
	function _createBackend()
  {
    $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
    return new lmbCacheFileBackend($this->cache_dir);
  }

  function _createPersisterImp()
  {
    return new lmbCacheGroupDecorator($this->_createBackend() , 'default_group');
  }

  function testPutAndReadWithOutGroup()
  {
    lmbToolkit::instance()->setCache($this->_createBackend());
    lmbToolkit::instance()->getCache()->flush();

    $a=SomeClass::Foo('a');
    $b=SomeClass::Foo('b');

    $cache1=lmbToolkit::instance()->getCache();

    $a_read=$cache1->get('bara',array('group'=>'bar_group'));
    $b_read=$cache1->get('barb',array('group'=>'bar_group'));

    $this->assertEquals($a_read, $a);
    $this->assertEquals($b_read, $b);
  }


  function testPutAndReadWithGroup()
  {

    lmbToolkit::instance()->setCache($this->_createPersisterImp());
    lmbToolkit::instance()->getCache()->flush();

    $a=SomeClass::Foo('a');
    $b=SomeClass::Foo('b');

    $cache1=lmbToolkit::instance()->getCache();

    $a_read=$cache1->get('bara',array('group'=>'bar_group'));
    $b_read=$cache1->get('barb',array('group'=>'bar_group'));

    $this->assertEquals($a_read, $a);
    $this->assertEquals($b_read, $b);

    $a=SomeClass::Foo('a');
    $b=SomeClass::Foo('b');
  }

  function testManyPutAndReadWithGroupAndDestroyTolkitObjectCache()
  {

    lmbToolkit::instance()->setCache($this->_createPersisterImp());
    lmbToolkit::instance()->getCache()->flush();

    $a=SomeClass::Foo('a');
    $b=SomeClass::Foo('b');

    $cache1=lmbToolkit::instance()->getCache();

    $a_read=$cache1->get('bara',array('group'=>'bar_group'));
    $b_read=$cache1->get('barb',array('group'=>'bar_group'));

    $this->assertEquals($a_read, $a);
    $this->assertEquals($b_read, $b);

    $a=SomeClass::Foo('a');
    $b=SomeClass::Foo('b');

    //This need to destroy cache in toolkit
    lmbToolkit::instance()->setCache($this->_createPersisterImp());


    $cache1=lmbToolkit::instance()->getCache();

    $a_read=$cache1->get('bara',array('group'=>'bar_group'));
    $b_read=$cache1->get('barb',array('group'=>'bar_group'));

    $this->assertEquals($a_read, $a);
    $this->assertEquals($b_read, $b);

  }
}
