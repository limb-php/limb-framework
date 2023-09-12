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
use limb\macro\src\tags\form\lmbMacroFormWidget;
use limb\macro\src\tags\form\lmbMacroFormElementWidget;
use limb\macro\src\tags\form\lmbMacroFormLabelWidget;
use limb\macro\src\tags\form\lmbMacroFormErrorList;

class lmbMacroFormWidgetTest extends lmbBaseMacroTestCase
{
  function testAddAndGetChild()
  {
    $form = new lmbMacroFormWidget('my_id');
    $field1 = new lmbMacroFormElementWidget('Input1');
    $field2 = new lmbMacroFormElementWidget('Input3');
    $form->addChild($field1);
    $form->addChild($field2);
    
    $this->assertEquals($form->getChild('Input3'), $field2);
    $this->assertNull($form->getChild('NoSuchInput'));
  }
  
  function testGetLabelFor()
  {
    $form = new lmbMacroFormWidget('my_id');
    $field1 = new lmbMacroFormElementWidget('Input1');
    $label1 = new lmbMacroFormLabelWidget('Label1');
    $label1->setAttribute('for', 'Input1');
    
    $field2 = new lmbMacroFormElementWidget('Input3');
    $label2 = new lmbMacroFormLabelWidget('Label3');
    $label2->setAttribute('for', 'Input3');

    $form->addChild($field1);
    $form->addChild($field2);
    $form->addChild($label1);
    $form->addChild($label2);
    
    $this->assertEquals($form->getLabelFor('Input3'), $label2);
    $this->assertEquals($form->getLabelFor('Input1'), $label1);
    $this->assertNull($form->getLabelFor('NoSuchInput'));
  }
  
  function testSetErrorsNotifyFieldsAboutErrors()
  {
    $error_list = new lmbMacroFormErrorList();
    $error_fields = array('x'=>'Input1', 'z'=>'Input3');
    $error_list->addError('message', $error_fields);

    $form = new lmbMacroFormWidget('my_id');
    $field1 = new lmbMacroFormElementWidget('Input1');
    $field2 = new lmbMacroFormElementWidget('Input3');
    $form->addChild($field1);
    $form->addChild($field2);
    
    $form->setErrorList($error_list);
    
    $this->assertTrue($field1->hasErrors());
    $this->assertTrue($field2->hasErrors());
  }
  
  function testSetErrorsConvertErrorsToErrorList()
  {
    $error_fields = array('x'=>'Input1', 'z'=>'Input3');
    $errors = array(array('message' => 'My message', 'fields' => $error_fields, 'values' => array(10, 20)));

    $form = new lmbMacroFormWidget('my_id');
    $form->setErrorList($errors);
    
    $error_list = $form->getErrorList();
    $this->assertInstanceOf(lmbMacroFormErrorList::class, $error_list);
    $this->assertCount(1, $error_list);
  }

  function testSetErrorsNotifyFieldsAndLabelsAboutErrors()
  {
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('message', array('x'=>'Input1'));

    $form = new lmbMacroFormWidget('my_id');
    $field1 = new lmbMacroFormElementWidget('Input1');
    $label1 = new lmbMacroFormLabelWidget('Label1');
    $label1->setAttribute('for', 'Input1');

    $field2 = new lmbMacroFormElementWidget('Input3');
    $label2 = new lmbMacroFormLabelWidget('Label3');
    $label2->setAttribute('for', 'Input3');
    
    $form->addChild($field1);
    $form->addChild($field2);
    $form->addChild($label1);
    $form->addChild($label2);
    
    $form->setErrorList($error_list);
    
    $this->assertTrue($field1->hasErrors());
    $this->assertFalse($field2->hasErrors());
    $this->assertTrue($label1->hasErrors());
    $this->assertFalse($label2->hasErrors());
  }

  function testGetFieldErrorsForField()
  {
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('message1', array('x'=>'Input1'));
    $error_list->addError('message2', array('x'=>'Input1', 'z'=>'Input2'));

    $form = new lmbMacroFormWidget('my_id');
    $form->setErrorList($error_list);

    $errors = $form->getErrorsListForFields();
    $this->assertEquals(3, sizeof($errors));
    
    $errors = $form->getErrorsListForFields('Input1');

    $this->assertEquals('message1', $errors[0]['message']);
    $this->assertEquals('message2', $errors[1]['message']);

    $errors = $form->getErrorsListForFields('Input2');
    $this->assertEquals('message2', $errors[0]['message']);
  }
}
