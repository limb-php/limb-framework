<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\view\cases;

require_once '.setup.php';

use PHPUnit\Framework\TestCase;
use limb\view\src\lmbMacroView;
use limb\fs\src\lmbFs;
use limb\validation\src\lmbErrorList;
use limb\core\src\lmbSet;
use limb\core\src\lmbEnv;

class lmbMacroViewTest extends TestCase
{
  function setUp(): void
  {
    lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/tpl/');
    lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tpl/');
  }

  function testRenderSimpleVars()
  {
      $template_name = 'test.phtml';
      $tpl = $this->_createTemplate('{$#hello}{$#again}', $template_name);
      $tpl_no_ext = str_replace(lmbMacroView::EXTENSION, '', $tpl);

      $view = $this->_createView($tpl_no_ext)
          ->set('hello', 'Hello message!')
          ->set('again', 'Hello again!');

      $this->assertEquals('Hello message!Hello again!', $view->render());

      $view = $this->_createView($tpl)
          ->set('hello', 'Hello message!')
          ->set('again', 'Hello again!');

      $this->assertEquals('Hello message!Hello again!', $view->render());
  }
  
  function testRenderForms()
  {
    $template = '{{form id="form1" name="form1"}}'.
                '{{form:errors to="$form_errors"/}}'.
                '{{list using="$form_errors" as="$item"}}{{list:item}}{$item.message}|{{/list:item}}{{/list}}'.     
                '{{input type="text" name="title" title="Title" /}}'.
                '{{/form}}';

    $tpl = $this->_createTemplate($template, 'test.phtml');
    $view = $this->_createView($tpl);

    $error_list = new lmbErrorList();
    $error_list->addError('An error in {Field} with {Value}', array('Field' => 'title'), array('Value' => 'value1'));

    $view->setFormDatasource('form1', new lmbSet(array('title' => 'My title')));
    $view->setFormErrors('form1', $error_list);

    $expected = '<form id="form1" name="form1">An error in &quot;Title&quot; with value1|'.
                '<input type="text" name="title" title="Title" value="My title" />'.
                '</form>';
                
    $this->assertEquals($view->render(), $expected);
  }   

  protected function _createView($file)
  {
    $view = new lmbMacroView($file);
    return $view;
  }

  protected function _createTemplate($code, $name)
  {
    $file = lmbEnv::get('LIMB_VAR_DIR') . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }
}
