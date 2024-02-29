<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\core\cases;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\exception\lmbInvalidArgumentException;
use limb\core\src\lmbAssert;
use Tests\core\cases\src\extStdClass;

class lmbAssertFunctionsTest extends TestCase
{
    function testAssertTrue()
    {
        $this->_checkPositive('true', true);
        $this->_checkNegative('true', false);

        $this->_checkPositive('true', 1);
        $this->_checkNegative('true', 0);

        $this->_checkPositive('true', 1.1);
        $this->_checkNegative('true', 0.0);

        $this->_checkPositive('true', 'foo');
        $this->_checkNegative('true', '');

        $this->_checkPositive('true', array(1));
        $this->_checkNegative('true', array());

        $this->_checkPositive('true', new \stdClass());
    }

    function testAssertTrue_DefaultMessage()
    {
        try {
            lmbAssert::assert_true(false);
            $this->fail();
        } catch (lmbInvalidArgumentException $e) {
            $this->assertMatchesRegularExpression('/Value must be true/', $e->getMessage());
        }
    }

    function testAssertTrue_CustomMessage()
    {
        $message = uniqid('lmb_assert_true');
        try {
            lmbAssert::assert_true(false, $message);
        } catch (lmbInvalidArgumentException $e) {
            $this->assertMatchesRegularExpression('/' . $message . '/', $e->getMessage());
        }
    }

    function testAssertType_Bool()
    {
        $types = array(
            array(
                'names' => array('bool', 'boolean'),
                'values' => array(true, false)
            ),
            array(
                'names' => array('integer', 'numeric', 'int'),
                'values' => array(42, 0, -1)
            ),
            array(
                'names' => array('float', 'double', 'real'),
                'values' => array(42.1, 0.0, 0xffffffffffffffff)
            ),
            array(
                'names' => array('string'),
                'values' => array('test'),
            ),
            array(
                'names' => array('array'),
                'values' => array(array())
            ),
            array(
                'names' => array('object'),
                'values' => array(new \stdClass())
            ),
        );

        foreach ($types as $type) {
            foreach ($type['names'] as $type_name) {
                foreach ($type['values'] as $value) {
                    $this->_checkPositive('type', $value, $type_name);
                }
            }
        }

        foreach ($types as $key => $type) {
            foreach ($types as $another_key => $another_type) {
                if ($key == $another_key)
                    continue;

                foreach ($type['names'] as $type_name) {
                    foreach ($another_type['values'] as $another_type_value) {
                        $this->_checkNegative('type', $another_type_value, $type_name);
                    }
                }
            }
        }
    }

    function testAssertType_ArrayAccessAsArray()
    {
        $this->_checkNegative('type', new \stdClass(), 'array');
        $this->_checkPositive('type', new \ArrayObject(), 'array');
    }

    function testAssertType_Objects()
    {
        $this->_checkPositive('type', new \ArrayObject(), 'ArrayAccess');
        $this->_checkPositive('type', new \ArrayObject(), 'ArrayObject');

        $this->_checkNegative('type', new \ArrayObject(), 'Foo');
    }

    function testAssertType_CustomMessage()
    {
        $message = uniqid('lmb_assert_type');
        try {
            lmbAssert::assert_type(true, 'string', $message);
        } catch (lmbInvalidArgumentException $e) {
            $this->assertMatchesRegularExpression('/' . $message . '/', $e->getMessage());
        }
    }

    function testAssertArrayWithKey()
    {
        $this->_checkNegative('array_with_key', 'not_array', 'needle');
        $this->_checkNegative('array_with_key', array('foo' => 1), 'bar');

        $this->_checkPositive('array_with_key', array('foo' => 1), 'foo');
        $this->_checkPositive('array_with_key', new \ArrayObject(array('foo' => 1)), 'foo');
    }

    function testAssertArrayWithKey_MultipleCheck()
    {
        //$this->_checkNegative('array_with_key', array('foo' => 1, 'bar' => 2), array('foo', 'baz'));

        $this->_checkPositive('array_with_key', array('foo' => 1, 'bar' => 2), array('foo', 'bar'));
    }

    function testAssertArrayWithKey_CustomMessage()
    {
        $message = uniqid('lmb_assert_array_with_key');
        try {
            lmbAssert::assert_array_with_key(array(), 'some_key', $message);
        } catch (lmbInvalidArgumentException $e) {
            $this->assertMatchesRegularExpression('/' . $message . '/', $e->getMessage());
        }
    }

    function testAssertRegExp()
    {
        $this->_checkNegative('reg_exp', array(), 'foo');

        $this->_checkPositive('reg_exp', 'foomatic', 'foo');
        $this->_checkNegative('reg_exp', 'bar', 'foo');

        $this->_checkPositive('reg_exp', 'abc', '/bc/');
        $this->_checkNegative('reg_exp', 'abc', '/xy/');

        $stub = $this->createMock(extStdClass::class);
        $stub->
        method('__toString')->
        willReturn('abc');

        $this->_checkPositive('reg_exp', $stub, '/bc/');
        $this->_checkNegative('reg_exp', $stub, '/xy/');
    }

    function testAssertRegExp_CustomMessage()
    {
        $message = uniqid('lmb_assert_reg_exp');
        try {
            lmbAssert::assert_reg_exp(array(), 'foo', $message);
        } catch (lmbInvalidArgumentException $e) {
            $this->assertMatchesRegularExpression('/' . $message . '/', $e->getMessage());
        }
    }

    protected function _callCheck($check_name, $first_check_param, $second_check_param)
    {
        return call_user_func_array('limb\core\src\lmbAssert::assert_' . $check_name, array($first_check_param, $second_check_param));
    }

    protected function _checkPositive($check_name, $first_check_param, $second_check_param = null)
    {
        $result = $this->_callCheck($check_name, $first_check_param, $second_check_param);
        $this->assertTrue(true);
    }

    protected function _checkNegative($check_name, $first_param, $second_param = null)
    {
        try {
            $result = $this->_callCheck($check_name, $first_param, $second_param);

            //$message = "fail lmbAssert::assert_{$check_name}(".(var_export($first_param, true)).", ".var_export($second_param, true).')';
            $this->fail();
        } catch (lmbInvalidArgumentException $e) {
            $this->assertTrue(true);
            return $e->getMessage();
        }
    }
}
