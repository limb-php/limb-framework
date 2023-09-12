<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\macro\cases\tags\form;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroJsCheckboxTagTest extends lmbBaseMacroTestCase
{
  function testRenderHiddenWithCheckbox()
  {
    $template = '{{js_checkbox name="my_checkbox" value="$#var"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 1);

    $html = new \SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEquals('checkbox', $html->input[0]['type']);

    $this->assertEquals('hidden', $html->input[1]['type']);
    $this->assertEquals('my_checkbox', $html->input[1]['name']);
    $this->assertEquals('0', $html->input[1]['value']);
  }

  function testRenderHiddenWithCheckedCheckbox()
  {
    $template = '{{js_checkbox name="my_checkbox" value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 1);

    $html = new \SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEquals('hidden', $html->input[1]['type']);
    $this->assertEquals('1', $html->input[1]['value']);
  }

  function testChecked_With_CheckedValueAttribute()
  {
    $template = '{{js_checkbox name="my_checkbox" checked_value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 1);

    $html = new \SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEquals('checked', $html->input[0]['checked']);
    $this->assertEquals('1', $html->input[1]['value']);
  }

  function testNotChecked_With_CheckedValueAttribute_And_ValueAttribute()
  {
    $template = '{{js_checkbox name="my_checkbox" checked_value="$#var" value="1" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 2);

    $html = new \SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEquals('checkbox', $html->input[0]['type']);
    $this->assertEquals('1', $html->input[0]['value']);

    $this->assertEquals('hidden', $html->input[1]['type']);
    $this->assertEquals('0', $html->input[1]['value']);
  }

  function testIdConformsW3C()
  {
    $template = '{{js_checkbox name="my_checkbox" value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 1);

    $html = new \SimpleXMLElement('<foo>'.$page->render().'</foo>');
    $error_message = 'Id must start from letter, that must be followed by letters, digits, underscores, colons and dots';

    $this->assertMatchesRegularExpression('~[a-z][a-z\d_:.]~i', $html->input[1]['id'], $error_message);
  }
}
