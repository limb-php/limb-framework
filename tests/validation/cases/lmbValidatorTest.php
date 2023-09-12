<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\validation\cases;

use PHPUnit\Framework\TestCase;
use limb\validation\src\lmbValidator;
use limb\validation\src\rule\lmbValidationRuleInterface;
use limb\validation\src\lmbErrorList;
use limb\core\src\lmbSet;

class lmbValidatorTest extends TestCase
{
  var $error_list;
  var $validator;

  function setUp(): void
  {
      parent::setUp();

      $this->error_list = $this->createMock(lmbErrorList::class);
      $this->validator = new lmbValidator();
      $this->validator->setErrorList($this->error_list);
  }

  function testValidateEmpty()
  {
    $validator = new lmbValidator();
    $this->assertTrue($validator->validate(new lmbSet()));
  }

  function testIsValid()
  {
    $this->error_list
        ->expects($this->exactly(2))
        ->method('isValid')
        ->will($this->onConsecutiveCalls(false, true));

    $this->assertFalse($this->validator->isValid());
    $this->assertTrue($this->validator->isValid());
  }

  function testAddRulesAndValidate()
  {
    $ds = new lmbSet(array('foo'));

    $r1 = $this->createMock(lmbValidationRuleInterface::class);
    $r2 = $this->createMock(lmbValidationRuleInterface::class);

    $this->validator->addRule($r1);
    $this->validator->addRule($r2);

    $r1->expects($this->once())
        ->method('validate')
        ->with($ds, $this->error_list);
    $r2->expects($this->once())
        ->method('validate')
        ->with($ds, $this->error_list);

    $this->error_list->expects($this->once())
        ->method('isValid')
        ->willReturn(true);

    $this->assertTrue($this->validator->validate($ds));
  }
}
