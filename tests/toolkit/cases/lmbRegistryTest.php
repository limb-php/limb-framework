<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\toolkit\cases;

use limb\core\src\exception\lmbException;
use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbRegistry;

class lmbRegistryTest extends TestCase
{
  function testGetNull()
  {
    $this->assertNull(lmbRegistry::get('Foo'));
  }

  function testSetGet()
  {
    lmbRegistry::set('Foo', 'foo');
    $this->assertEquals('foo', lmbRegistry::get('Foo'));
  }

  function testSaveRestore()
  {
    lmbRegistry::set('Foo', 'foo');

    lmbRegistry::save('Foo');
    $this->assertEquals(null, lmbRegistry::get('Foo'));

    lmbRegistry::set('Foo', 'bar');
    $this->assertEquals('bar', lmbRegistry::get('Foo'));

    lmbRegistry::save('Foo');
    $this->assertEquals(null, lmbRegistry::get('Foo'));

    lmbRegistry::set('Foo', 'baz');
    $this->assertEquals('baz', lmbRegistry::get('Foo'));

    lmbRegistry::restore('Foo');
    $this->assertEquals('bar', lmbRegistry::get('Foo'));

    lmbRegistry::restore('Foo');
    $this->assertEquals('foo', lmbRegistry::get('Foo'));
  }

  function testRestoreException()
  {
    try
    {
      lmbRegistry::restore('No-such');
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testSaveException()
  {
    try
    {
      lmbRegistry::save('No-such');
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }
}
