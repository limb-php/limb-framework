<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\validation\cases\rule;

use limb\validation\src\rule\DomainRule;
use limb\core\src\lmbSet;

require('.setup.php');

class lmbDomainRuleTest extends lmbValidationRuleTestCase
{
  function testDomainRule()
  {
    $rule = new DomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => 'sourceforge.net'));

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleBlank()
  {
    $rule = new DomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => ''));

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleBadCharacters()
  {
    $rule = new DomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => 'source#&%forge.net'));

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} must contain only letters, numbers, hyphens, and periods.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array());

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleDoubleHyphens()
  {
    $rule = new DomainRule('testfield');

    $dataspace = new lmbSet(array('testfield' => 'source--forge.net'));

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} may not contain double hyphens (--).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array());

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleTooLarge()
  {
    $rule = new DomainRule('testfield');

    $segment = "abcdefg-hijklmnop-qrs-tuv-wx-yz-ABCDEFG-HIJKLMNOP-QRS-TUV-WX-YZ-0123456789";

    $dataspace = new lmbSet();
    $dataspace->set('testfield', $segment . '.net');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} segment {segment} is too large (it must be 63 characters or less).', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('segment'=>$segment));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainHyphenBegin()
  {
    $rule = new DomainRule('testfield');

    $segment = "-sourceforge";

    $dataspace = new lmbSet();
    $dataspace->set('testfield', $segment . '.net');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} segment {segment} may not begin or end with a hyphen.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('segment'=>$segment));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleHyphenEnd()
  {
    $rule = new DomainRule('testfield');

    $segment = "sourceforge-";

    $dataspace = new lmbSet();
    $dataspace->set('testfield', $segment . '.net');

    $this->error_list
        ->expects($this->once())
        ->method('addError')
        ->with(lmb_i18n('{Field} segment {segment} may not begin or end with a hyphen.', 'validation'),
                                        array('Field'=>'testfield'),
                                        array('segment'=>$segment));

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleCombination()
  {
    $rule = new DomainRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', '.n..aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.');

    $this->error_list
        ->expects($this->exactly(4))
        ->method('addError');

    $this->error_list
        ->method('addError')
        ->withConsecutive(
            [
                lmb_i18n('{Field} cannot start with a period.', 'validation'),
                array('Field'=>'testfield'),
                array()
            ],
            [
                lmb_i18n('{Field} cannot end with a period.', 'validation'),
                array('Field'=>'testfield'),
                array()
            ],
            [
                lmb_i18n('{Field} may not contain double periods (..).', 'validation'),
                array('Field'=>'testfield'),
                array()
            ],
            [
                lmb_i18n('{Field} segment {segment} is too large (it must be 63 characters or less).', 'validation'),
                array('Field'=>'testfield'),
                array('segment' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')
            ]
        );

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleDoubleDomain()
  {
    $rule = new DomainRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'microsoft.co.uk');

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testDomainRuleLocalDomain()
  {
    $rule = new DomainRule('testfield');

    $dataspace = new lmbSet();
    $dataspace->set('testfield', 'localhost');

    $this->error_list->expects($this->never())->method('addError');

    $rule->validate($dataspace, $this->error_list);
  }
}
