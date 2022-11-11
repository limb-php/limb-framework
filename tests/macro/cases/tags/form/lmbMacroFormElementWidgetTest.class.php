<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\macro\cases\tags\form;

use tests\macro\cases\lmbBaseMacroTest;

class lmbMacroFormElementWidgetTest extends lmbBaseMacroTest
{
  function testGetValue_FromValueAttribute()
  {
    $form = new lmbMacroFormWidget('my_id');
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('value', 10);
    $widget->setForm($form);
    
    $this->assertEquals($widget->getValue(), 10);
  }

  function testGetValue_FromFormDatasource()
  {
    $form = new lmbMacroFormWidget('my_id');
    $form->setDatasource(array('any_field' => 10));
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setForm($form);
    
    $this->assertEquals($widget->getValue(), 10);
  }

  function testGetValue_FromFormDatasource_ByNameAttribute()
  {
    $form = new lmbMacroFormWidget('my_id');
    $form->setDatasource(array('any_field' => 'wrong_value', 'field_name' => 10));
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('name', 'field_name');
    $widget->setForm($form);
    
    $this->assertEquals($widget->getValue(), 10);
  }
  
  function testGetDisplayName_ReturnIdByDefault()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $this->assertEquals($widget->getDisplayname(), 'any_field');
  }

  function testGetDisplayName_ReturnTitleAttribute()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('title', 'My Field');
    $this->assertEquals($widget->getDisplayname(), 'My Field');
  }

  function testGetDisplayName_ReturnAltAttribute()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('alt', 'My Super Field');
    $this->assertEquals($widget->getDisplayname(), 'My Super Field');
  }
  
  function testSetErrorState_SetErrorStateClassAndStyle()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('error_style', 'my_error_style');
    $widget->setAttribute('error_class', 'my_error_class');
    $widget->setErrorState(true);
    
    $this->assertEquals($widget->getAttribute('class'), 'my_error_class');
    $this->assertEquals($widget->getAttribute('style'), 'my_error_style');
  }
}
