<?php

namespace Limb\Tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\lmbEnv;

class lmbEnvFunctionsTest extends TestCase
{
    private $_prev_env = array();
    private $_keys = array();

    protected function setUp(): void
    {
        $this->_prev_env = $_ENV;
        $_ENV = array();
        $this->_keys = array();
    }

    protected function tearDown(): void
    {
        $_ENV = $this->_prev_env;
    }

    function testGetNullByDefault()
    {
        $this->assertNull(lmbEnv::get($this->_('foo')));
    }

    function testGetDefault()
    {
        $this->assertEquals(1, lmbEnv::get($this->_('foo'), 1));
    }

    function testGetWithDefinedConstant()
    {
        lmbEnv::set($this->_('foo'), 'bar');
        $this->assertEquals('bar', lmbEnv::get($this->_('foo')));
    }

    function testSet()
    {
        lmbEnv::set($this->_('foo'), 'bar');
        $this->assertEquals('bar', lmbEnv::get($this->_('foo')));
    }

    function testSetOr()
    {
        lmbEnv::setor($this->_('foo'), 'bar');
        $this->assertEquals('bar', lmbEnv::get($this->_('foo')));

        lmbEnv::setor($this->_('foo'), 'baz');
        $this->assertEquals('bar', lmbEnv::get($this->_('foo')));
    }

    function testSetOrWithDefinedConstant()
    {
        lmbEnv::set($this->_('foo'), 'bar');

        lmbEnv::setor($this->_('foo'), 'baz');
        $this->assertEquals('bar', lmbEnv::get($this->_('foo')));
    }

    function testHas()
    {
        $this->assertFalse(lmbEnv::has($this->_('foo')));
        lmbEnv::set($this->_('foo'), 'bar');
        $this->assertTrue(lmbEnv::has($this->_('foo')));
    }

    function testHasWorksForNulls()
    {
        $this->assertFalse(lmbEnv::has($this->_('foo')));
        lmbEnv::set($this->_('foo'), null);
        $this->assertTrue(lmbEnv::has($this->_('foo')));
    }

    function testSetDefinesConstant()
    {
        $this->assertFalse(lmbEnv::has($this->_('foo')));
        lmbEnv::set($this->_('foo'), 'bar');
        $this->assertEquals('bar', lmbEnv::get($this->_('foo')));
    }

    function testHasAndGetFallbackToConstant()
    {
        $name = $this->_('LIMB_TEST_FOO');

        $this->assertFalse(lmbEnv::has($name));
        $this->assertNull(lmbEnv::get($name, null));

        lmbEnv::set($name, 'bar');
        $this->assertTrue(lmbEnv::has($name));
        $this->assertEquals('bar', lmbEnv::get($name));
    }

    function testRemove()
    {
        lmbEnv::set('foo_remove', 'bar');
        $this->assertTrue(lmbEnv::has('foo_remove'));
        $this->assertEquals('bar', lmbEnv::get('foo_remove'));

        lmbEnv::remove('foo_remove');
        $this->assertFalse(lmbEnv::has('foo_remove'));
        $this->assertEquals(lmbEnv::get('foo_remove', $random = mt_rand()), $random);
    }

    function testTrace()
    {
        lmbEnv::trace($this->_('foo'));

        ob_start();
        lmbEnv::setor($key = $this->_('foo'), $value = 'bar');
        $call_line = strval(__LINE__ - 1);
        $trace_info = ob_get_clean();

        $this->assertTrue(strstr($trace_info, __FILE__) !== false);
        $this->assertTrue(strstr($trace_info, $call_line) !== false);
        $this->assertTrue(strstr($trace_info, $method_name = 'setor') !== false);
        $this->assertTrue(strstr($trace_info, $key) !== false);
        $this->assertTrue(strstr($trace_info, $value) !== false);

        ob_start();
        lmbEnv::set($key, $value = 'baz');
        $call_line = strval(__LINE__ - 1);
        $trace_info = ob_get_clean();

        $this->assertTrue(strstr($trace_info, __FILE__) !== false);
        $this->assertTrue(strstr($trace_info, $call_line) !== false);
        $this->assertTrue(strstr($trace_info, $method_name = 'set') !== false);
        $this->assertTrue(strstr($trace_info, $key) !== false);
        $this->assertTrue(strstr($trace_info, $value) !== false);
    }

    //used for convenient tracking of the random keys
    private function _($name)
    {
        if (!isset($this->_keys[$name]))
            $this->_keys[$name] = $name . mt_rand() . time();
        return $this->_keys[$name];
    }

    function testLmbVarDir_Get()
    {
        $old_value = lmbEnv::get('LIMB_VAR_DIR');
        $new_value = $old_value . '/';

        lmbEnv::set('LIMB_VAR_DIR', $new_value);
        $this->assertEquals($new_value, lmb_var_dir());

        lmbEnv::set('LIMB_VAR_DIR', $old_value);
    }

    function testLmbVarDir_Set()
    {
        $old_value = lmbEnv::get('LIMB_VAR_DIR');
        $new_value = $old_value . '/';

        lmb_var_dir($new_value);
        $this->assertEquals($new_value, lmb_var_dir());

        lmbEnv::set('LIMB_VAR_DIR', $old_value);
    }
}
