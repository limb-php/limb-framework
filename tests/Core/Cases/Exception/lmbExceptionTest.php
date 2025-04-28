<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\Core\Cases\Exception;

use PHPUnit\Framework\TestCase;
use Limb\Core\Exception\lmbException;

class lmbExceptionTest extends TestCase
{
    function testGetParams()
    {
        $e = new lmbException('foo', $params = array('bar' => 'baz'));
        $this->assertEquals($params, $e->getParams());
    }

    function testGetParam()
    {
        $e = new lmbException('foo', array('bar' => 'baz'));
        $this->assertEquals('baz', $e->getParam('bar'));
        $this->assertNull($e->getParam('not_existed'));
    }

    function testGetMessage()
    {
        $original_message = 'foo';
        $param_key = 'bar';
        $param_value = 'baz';
        $e = new lmbException($original_message, array($param_key => $param_value));
        $this->assertMatchesRegularExpression("/{$original_message}/", $e->getMessage());
        $this->assertMatchesRegularExpression("/{$param_key}/", $e->getMessage());
        $this->assertMatchesRegularExpression("/{$param_value}/", $e->getMessage());
    }

    function testGetNiceTraceAsString()
    {
        $e = $this->_createException('foo');
        $trace = $e->getNiceTraceAsString();
        $trace_arr = explode(PHP_EOL, $trace);
        $first_call = array_shift($trace_arr);

        $this->assertMatchesRegularExpression('/lmbExceptionTest/', $first_call);
        $this->assertMatchesRegularExpression('/_createException/', $first_call);
        $this->assertMatchesRegularExpression('/foo/', $first_call);
        $this->assertMatchesRegularExpression('/' . basename(__FILE__) . '/', $first_call);
        $this->assertMatchesRegularExpression('/41/', $first_call);
    }

    function testGetNiceTraceAsString_HideCalls()
    {
        $full = new lmbException('foo', array(), 0);
        $with_hidden_call = new lmbException('foo', array(), 0, 1);

        $trace_full = explode(PHP_EOL, $full->getNiceTraceAsString());
        $trace_with_hidden_call = explode(PHP_EOL, $with_hidden_call->getNiceTraceAsString());

        $this->assertEquals($trace_full[1], $trace_with_hidden_call[0]);
    }

    protected function _createException()
    {
        return new lmbException('foo');
    }
}
