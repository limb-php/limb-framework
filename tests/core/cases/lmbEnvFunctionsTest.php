<?php

use limb\core\src\lmbEnv;

class lmbEnvFunctionsTest extends UnitTestCase
{
  private $_prev_env = array();
  private $_keys = array();

  function setUp()
  {
    $this->_prev_env = $_ENV;
    $_ENV = array();
    $this->_keys = array();
  }

  function tearDown()
  {
    $_ENV = $this->_prev_env;
  }

  function testGetNullByDefault()
  {
    $this->assertNull(lmbEnv::get($this->_('foo')));
  }

  function testGetDefault()
  {
    $this->assertEqual(lmbEnv::get($this->_('foo'), 1), 1);
  }

  function testGetWithDefinedConstant()
  {
      define($this->_('foo'), 'bar');
      $this->assertEqual(lmbEnv::get($this->_('foo')), 'bar');
  }

  function testSet()
  {
    lmbEnv::set($this->_('foo'), 'bar');
    $this->assertEqual(lmbEnv::get($this->_('foo')), 'bar');
  }

  function testSetOr()
  {
    lmbEnv::setor($this->_('foo'), 'bar');
    $this->assertEqual(lmbEnv::get($this->_('foo')), 'bar');

    lmbEnv::setor($this->_('foo'), 'baz');
    $this->assertEqual(lmbEnv::get($this->_('foo')), 'bar');
  }

  function testSetOrWithDefinedConstant()
  {
      define($this->_('foo'), 'bar');

      lmbEnv::setor($this->_('foo'), 'baz');
      $this->assertEqual(lmbEnv::get($this->_('foo')), 'bar');
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
    $this->assertFalse(defined($this->_('foo')));
    lmbEnv::set($this->_('foo'), 'bar');
    $this->assertEqual(constant($this->_('foo')), 'bar');
  }

  function testHasAndGetFallbackToConstant()
  {
    $name = $this->_('LIMB_TEST_FOO');

    $this->assertFalse(lmbEnv::has($name));
    $this->assertNull(lmbEnv::get($name, null));

    define($name, 'bar');
    $this->assertTrue(lmbEnv::has($name));
    $this->assertEqual(lmbEnv::get($name), 'bar');
  }

  function testRemove()
  {
    lmbEnv::set('foo_remove', 'bar');
    $this->assertTrue(lmbEnv::has('foo_remove'));
    $this->assertEqual(lmbEnv::get('foo_remove'), 'bar');

    lmbEnv::remove('foo_remove');
    $this->assertFalse(lmbEnv::has('foo_remove'));
    $this->assertEqual(lmbEnv::get('foo_remove', $random = mt_rand()), $random);
  }

  function testTrace()
  {
    lmbEnv::trace($this->_('foo'));

    ob_start();
    lmbEnv::setor($key = $this->_('foo'), $value = 'bar');
    $call_line = strval(__LINE__ - 1);
    $trace_info = ob_get_clean();

    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line));
    $this->assertTrue(strstr($trace_info, $method_name = 'setor'));
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));

    ob_start();
    lmbEnv::set($key, $value = 'baz');
    $call_line = strval(__LINE__ - 1);
    $trace_info = ob_get_clean();

    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line));
    $this->assertTrue(strstr($trace_info, $method_name = 'set'));
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));
  }

  //used for convenient tracking of the random keys
  private function _($name)
  {
    if(!isset($this->_keys[$name]))
      $this->_keys[$name] = $name . mt_rand() . time();
    return $this->_keys[$name];
  }

  function testLmbVarDir_Get() {
    $old_value = lmbEnv::get('LIMB_VAR_DIR');
    $new_value = $old_value.'/';

    lmbEnv::set('LIMB_VAR_DIR', $new_value);
    $this->assertIdentical($new_value, lmb_var_dir());

    lmbEnv::set('LIMB_VAR_DIR', $old_value);
  }

  function testLmbVarDir_Set() {
    $old_value = lmbEnv::get('LIMB_VAR_DIR');
    $new_value = $old_value.'/';

    lmb_var_dir($new_value);
    $this->assertIdentical($new_value, lmb_var_dir());

    lmbEnv::set('LIMB_VAR_DIR', $old_value);
  }
}
