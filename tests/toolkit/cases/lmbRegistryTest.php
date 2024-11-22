<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\toolkit\cases;

use limb\core\src\exception\lmbException;
use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbRegistry;

require_once (dirname(__FILE__) . '/.setup.php');

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
        try {
            lmbRegistry::restore('No-such');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testSaveException()
    {
        try {
            lmbRegistry::save('No-such');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }
}
