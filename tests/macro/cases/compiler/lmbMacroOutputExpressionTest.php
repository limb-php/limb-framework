<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use tests\macro\cases\lmbBaseMacroTestCase;
use limb\core\src\lmbObject;

class lmbMacroOutputExpressionTestClass
{
    public $zoo;

    function func1()
    {
        return 10;
    }

    function func2($param1, $param2)
    {
        return $param1 . ' - ' . $param2;
    }

    function func3($extra = '')
    {
        $res = array('zoo' => $this->zoo);
        if ($extra)
            $res['extra'] = $extra;

        return $res;
    }
}

class lmbMacroOutputExpressionTest extends lmbBaseMacroTestCase
{
    function testSimpleOutput()
    {
        $content = '{$#var}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', 'Foo');

        $out = $macro->render();
        $this->assertEquals('Foo', $out);
    }

    function testSimpleChainedOutputForArray()
    {
        $content = '{$#var.foo.bar}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', array('foo' => array('bar' => 'Hey')));

        $out = $macro->render();
        $this->assertEquals('Hey', $out);
    }

    function testBrokenChainOutputForArray()
    {
        $content = '{$#var.foo.bar.baz}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', null);
        $out = $macro->render();
        $this->assertEquals('', $out);

        $macro->set('var', array('foo' => null));
        $out = $macro->render();
        $this->assertEquals('', $out);

        $macro->set('var', array('foo' => array('bar' => null)));
        $out = $macro->render();
        $this->assertEquals('', $out);
    }

    function testSimpleChainedOutputForObject()
    {
        $content = '{$#var.foo.bar}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', new lmbObject(array('foo' => new lmbObject(array('bar' => 'Hey')))));

        $out = $macro->render();
        $this->assertEquals('Hey', $out);
    }

    function testBrokenChainOutputForObject()
    {
        $content = '{$#var.foo.bar.baz}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', null);
        $out = $macro->render();
        $this->assertEquals('', $out);

        $macro->set('var', new lmbObject(array('foo' => null)));
        $out = $macro->render();
        $this->assertEquals('', $out);

        $macro->set('var', new lmbObject(array('foo' => new lmbObject(array('bar' => null)))));
        $out = $macro->render();
        $this->assertEquals('', $out);
    }

    function testChainedOutputForMixedArraysAndObjects()
    {
        $content = '{$#var.foo.bar}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', new lmbObject(array('foo' => array('bar' => 'Hey'))));

        $out = $macro->render();
        $this->assertEquals('Hey', $out);
    }

    function testChainedOutputWithArrayIndexInPath()
    {
        $content = '{$#var.1.title}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', array(array('title' => 'First'), array('title' => 'Second')));

        $out = $macro->render();
        $this->assertEquals('Second', $out);
    }

    function testBrokenChainOutputForMixedArraysAndObjects()
    {
        $content = '{$#var.foo.bar.baz}';

        $macro = $this->_createMacroTemplate($content, 'tpl.html');

        $macro->set('var', new lmbObject(array('foo' => array('bar' => null))));
        $out = $macro->render();
        $this->assertEquals('', $out);
    }

    function testTemplateWithOutputExpression()
    {
        $code = '<h1>{$#bar}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', "foo");
        $out = $tpl->render();
        $this->assertEquals('<h1>foo</h1>', $out);
    }

    function testFunctionCallWithoutParamsInOutputExpression()
    {
        $code = '<h1>{$#bar->func1()}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', new lmbMacroOutputExpressionTestClass());
        $out = $tpl->render();
        $this->assertEquals('<h1>10</h1>', $out);
    }

    function testFunctionCallWithParamsInOutputExpression()
    {
        $code = '<h1>{$#bar->func2("aaa", $#foo)}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', new lmbMacroOutputExpressionTestClass());
        $tpl->set('foo', 10);
        $out = $tpl->render();
        $this->assertEquals('<h1>aaa - 10</h1>', $out);
    }

    function testFunctionCallAfterPathBasedChunkWithParamsInOutputExpression()
    {
        $code = '<h1>{$#bar.extra->func2("aaa", $#foo)}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', array('extra' => new lmbMacroOutputExpressionTestClass()));
        $tpl->set('foo', 10);
        $out = $tpl->render();
        $this->assertEquals('<h1>aaa - 10</h1>', $out);
    }

    function testPathAfterFunctionCallInOutputExpression()
    {
        $code = '<h1>{$#bar->func3().zoo}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $object = new lmbMacroOutputExpressionTestClass();
        $object->zoo = 30;
        $tpl->set('bar', $object);
        $out = $tpl->render();
        $this->assertEquals('<h1>30</h1>', $out);
    }

    function testPointInFuncParams()
    {
        $code = '<h1>{$#bar->func2(".", "+")}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', new lmbMacroOutputExpressionTestClass());
        $out = $tpl->render();
        $this->assertEquals('<h1>. - +</h1>', $out);
    }

    function testPointInFuncParamsAndComplexPathInOutputExpression()
    {
        $code = '<h1>{$#bar->func3(".").extra}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', new lmbMacroOutputExpressionTestClass());
        $out = $tpl->render();
        $this->assertEquals('<h1>.</h1>', $out);
    }

    function testClosingBracketInFuncParamsAndComplexPathInOutputExpression()
    {
        $code = '<h1>{$#bar->func3("}").extra}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', new lmbMacroOutputExpressionTestClass());
        $out = $tpl->render();
        $this->assertEquals('<h1>}</h1>', $out);
    }

    // Test that concat operation in function call is just concat operation
    function testComplexFuncParamsWithConcatOperationInOutputExpression()
    {
        $code = '<h1>{$#bar->func3($#foo["var1.var3"] . $#foo["var2"]).extra}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', new lmbMacroOutputExpressionTestClass());
        $tpl->set('foo', array('var1.var3' => 10, 'var2' => 20));

        $out = $tpl->render();

        $this->assertEquals('<h1>1020</h1>', $out);
    }

    function testNestedFunctionCalls()
    {
        $code = '<h1>{$#bar->func2($#foo->func2(10, 20), "aaa")}</h1>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('bar', new lmbMacroOutputExpressionTestClass());
        $tpl->set('foo', new lmbMacroOutputExpressionTestClass());

        $out = $tpl->render();

        $this->assertEquals('<h1>10 - 20 - aaa</h1>', $out);
    }

    function testOutputExpressionAsHtmlTagAttribute()
    {
        $code = '<a href="{$#href}">{$#message}</a>';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('href', '/path/to/somethere');
        $tpl->set('message', 'some text');
        $expected = '<a href="/path/to/somethere">some text</a>';

        $this->assertEquals($expected, $tpl->render());
    }
}
