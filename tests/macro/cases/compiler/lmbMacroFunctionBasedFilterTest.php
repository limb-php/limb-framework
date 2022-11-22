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
use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

class lmbMacroFunctionBasedFilterTestFunctionFilter extends lmbMacroFunctionBasedFilter
{
  protected $function = 'lmb_macro_function_based_filter_test_function';
}

class lmbMacroFunctionBasedFilterTestCallbackFilter extends lmbMacroFunctionBasedFilter 
{
  protected $function = array(lmbMacroFunctionBasedFilterTest::class, 'test');
}

class FakeBase
{
  protected $foo = 42;
  
  function getValue()
  {
    return $this->foo;
  }
}

class lmbMacroFunctionBasedFilterTest extends lmbBaseMacroTestCase
{
  function testFunction()
  {
    $fake_base = new FakeBase();
    $obj = new lmbMacroFunctionBasedFilterTestFunctionFilter($fake_base);
    $obj->setParams(array('"foo"'));
    $this->assertEquals('lmb_macro_function_based_filter_test_function(42,"foo")', $obj->getValue());
  }
  
  function testCallback()
  {
    $fake_base = new FakeBase();
    $obj = new lmbMacroFunctionBasedFilterTestCallbackFilter($fake_base);
    $obj->setParams(array('"foo"'));
    $this->assertEquals('tests\macro\cases\compiler\lmbMacroFunctionBasedFilterTest::test(42,"foo")', $obj->getValue());
  }
}
