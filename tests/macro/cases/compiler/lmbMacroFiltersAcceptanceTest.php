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
use limb\macro\src\compiler\lmbMacroFilter;
use limb\macro\src\compiler\lmbMacroFilterInfo;
use limb\macro\src\compiler\lmbMacroFilterDictionary;

class MacroFilterFooTest extends lmbMacroFilter
{
  function getValue()
  {
    return 'strtoupper(' . $this->base->getValue() . ')';
  }
}

class MacroFilterZooTest extends lmbMacroFilter
{
  function getValue()
  {
    if(!isset($this->params[0]))
      return 'trim(' . $this->base->getValue() . ')';
    else
      return 'trim(' . $this->base->getValue() . ', ' . $this->params[0] . ')';
  }
}

$foo_filter_info = new lmbMacroFilterInfo('uppercase', MacroFilterFooTest::class);
$foo_filter_info->setFile(__FILE__);
$zoo_filter_info = new lmbMacroFilterInfo('trim', MacroFilterZooTest::class);
$zoo_filter_info->setFile(__FILE__);

lmbMacroFilterDictionary::instance()->register($foo_filter_info);
lmbMacroFilterDictionary::instance()->register($zoo_filter_info);

class lmbMacroFiltersAcceptanceTest extends lmbBaseMacroTestCase
{
  function testFilter()
  {
    $code = '{$#var|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 'hello');
    $out = $tpl->render();
    $this->assertEquals('HELLO', $out);
  }

  function testFilterChain()
  {
    $code = '{$#var|trim|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  hello  ');
    $out = $tpl->render();
    $this->assertEquals('HELLO', $out);
  }
  
  function testFilterWithParams()
  {
    $code = '{$#var|trim|trim:"/"|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  /hello/  ');
    $out = $tpl->render();
    $this->assertEquals('HELLO', $out);
  }

  function testFilterWithVariablesInParams()
  {
    $code = '{$#var|trim|trim:$#foo|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  /hello/  ');
    $tpl->set('foo', '/');
    $out = $tpl->render();
    $this->assertEquals('HELLO', $out);
  }

  function testFilterWithPHPCodeInParams()
  {
    $code = '{$#var|trim|trim:$#foo . $#bar|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  #/hello/#  ');
    $tpl->set('foo', '/');
    $tpl->set('bar', '#');
    $out = $tpl->render();
    $this->assertEquals('HELLO', $out);
  }
  
  function testApplyHtmlFilterByDefault()
  {
    $code = '{$#var}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '<>');
    $out = $tpl->render();
    $this->assertEquals('&lt;&gt;', $out);
  }  

  function testDoesNotApplyHtmlFilterIfOutFilterPresent()
  {
    $code = '{$#var|trim}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '<>');
    $out = $tpl->render();
    $this->assertEquals('<>', $out);
  }    
}
