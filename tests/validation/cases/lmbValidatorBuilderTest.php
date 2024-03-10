<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases;

use PHPUnit\Framework\TestCase;
use limb\validation\src\lmbValidator;
use limb\validation\src\lmbValidatorBuilder;
use limb\core\src\lmbHandle;
use limb\validation\src\rule\RequiredRule;
use limb\validation\src\rule\MatchRule;
use limb\validation\src\rule\SizeRangeRule;
use limb\validation\src\rule\IdentifierRule;
use limb\validation\src\rule\EmailRule;
use limb\validation\src\rule\PatternRule;
use limb\web_app\src\validation\rule\UniqueTableFieldRule;

class lmbValidatorBuilderTest extends TestCase
{
    protected $validator;

    function setUp(): void
    {
        $this->validator = $this->createMock(lmbValidator::class);
    }

    function testAddRulesFromSimpleString()
    {
        $rules = array();
        $rules['login'] = "required|matches[bbb]|size_range[5, 8]|identifier";

        $this->validator
            ->expects($this->exactly(4))
            ->method("addRule")
            ->withConsecutive(
                [new lmbHandle(RequiredRule::class, array('login'))],
                [new lmbHandle(MatchRule::class, array('login', 'bbb'))],
                [new lmbHandle(SizeRangeRule::class, array('login', 5, 8))],
                [new lmbHandle(IdentifierRule::class, array('login'))]
            );

        lmbValidatorBuilder::addRules($rules, $this->validator);
    }

    function testAddRulesFromArrayWithCustomArguments()
    {
        $errors = array(
            'email' => 'Email error',
            'pattern' => 'Not a digit',
            'size_range' => 'Size range error!'
        );

        $rules = array();

        $rules['login'] = array(
            'required',
            'size_range[5, 8]',
            'email' => $errors['email'],
            'pattern[/\d+/]' => $errors['pattern'],
            'size_range[5, 8]' => array( // params [5, 8] will be ignored because of args have array type
                'min' => 10,
                'max' => 15,
                'error' => $errors['size_range']  // keys (min, max, error) are ignored, the order of args is still important
            )
        );

        $this->validator
            ->expects($this->exactly(5))
            ->method("addRule")
            ->withConsecutive(
                [new lmbHandle(RequiredRule::class, array('login'))],
                [new lmbHandle(SizeRangeRule::class, array('login', 5, 8))],
                [new lmbHandle(EmailRule::class, array('login', $errors['email']))],
                [new lmbHandle(PatternRule::class, array('login', '/\d+/', $errors['pattern']))],
                [new lmbHandle(SizeRangeRule::class, array('login', 10, 15, $errors['size_range']))]
            );

        lmbValidatorBuilder::addRules($rules, $this->validator);
    }

    function testAddCustomRules()
    {
        $rules = array();
        $rules['login'] = array(
            "unique_table_field" => array(
                'table' => 'user',
                'field' => 'login'
            )
        );

        $this->validator
            ->expects($this->once())
            ->method("addRule")
            ->with(
                new lmbHandle(UniqueTableFieldRule::class, array('login', 'user', 'login'))
            );

        lmbValidatorBuilder::addRules($rules, $this->validator);
    }
}
