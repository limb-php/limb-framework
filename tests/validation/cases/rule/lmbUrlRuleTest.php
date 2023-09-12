<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\validation\cases\rule;

use limb\validation\src\rule\UrlRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbUrlRuleTest extends lmbValidationRuleTestCase
{
  function testUrlRule()
  {
    $rule = new UrlRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'http://www.sourceforge.net/');

    $this->error_list->expects($this->never())->method('addError');
    $rule->validate($dataspace, $this->error_list);
    
    $dataspace->set('testfield', 'https://www.sourceforge.net/');
    $this->error_list->expects($this->never())->method('addError');
    $rule->validate($dataspace, $this->error_list);
        
    $dataspace->set('testfield', 'ftp://www.sourceforge.net/');
    $this->error_list->expects($this->never())->method('addError');
    $rule->validate($dataspace, $this->error_list);
  }
  
  function testUrlRuleWithoutSchema()
  {
    $rule = new UrlRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'www.sourceforge.net/');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(
        '{Field} is not an url.',
        array('Field'=>'testfield'),
        array());

    $rule->validate($dataspace, $this->error_list);
  }
  
  function testUrlRuleDomain()
  {
    $rule = new UrlRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'http://www.source--forge.net/');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(
        lmb_i18n('{Field} may not contain double hyphens (--).', 'validation'),
        array('Field'=>'testfield'),
        array()
        );

    $rule->validate($dataspace, $this->error_list);
  }

  function testUrlRuleDomainWithCustomError()
  {
    $rule = new UrlRule('testfield', 'Custom_Error');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'http://www.source--forge.net/');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(
        'Custom_Error',
        array('Field'=>'testfield'),
        array()
        );

    $rule->validate($dataspace, $this->error_list);
  }
  
  function testUrlRuleWithGarbage()
  {    
    $rule = new UrlRule('testfield');
    
    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'as@#$@$%ADGasjdkjf');
        
    $this->error_list
        ->expects($this->once())
        ->method('addError');
    
    $rule->validate($dataspace, $this->error_list);
  }
}
