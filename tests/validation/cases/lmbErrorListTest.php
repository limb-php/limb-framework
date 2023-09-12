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
use limb\validation\src\lmbErrorList;

class lmbErrorListTest extends TestCase
{
  function testAddFieldError()
  {
    $list = new lmbErrorList();

    $this->assertTrue($list->isValid());

    $list->addError($message = 'error_group', array('foo'), array('FOO'));

    $this->assertFalse($list->isValid());

    $errors = $list->export();

    $this->assertCount(1, $errors);
    $this->assertEquals($message, $errors['foo']['message']);
    $this->assertEquals(array('foo'), $errors['foo']['fields']);
    $this->assertEquals(array('FOO'), $errors['foo']['values']);
  }

    function testAddMultiFieldError()
    {
        $list = new lmbErrorList();

        $list->addError($message = 'error_group', array('foo'), array('FOO'));
        $list->addError($message = 'error_group_bar', array('bar'), array('BAR'));
        $errors = $list->export();

        $this->assertCount(2, $errors);

        $errors_for_foo = $list->getByKey('foo');

        $this->assertEquals('error_group', $errors_for_foo->getReadable());
    }

    function testAddSameMultiFieldError()
    {
        $list = new lmbErrorList();

        $list->addError($message = 'error_group1', array('foo'), array('FOO1'), 'foo1');
        $list->addError($message = 'error_group2', array('foo'), array('FOO2'), 'foo2');
        $errors = $list->export();

        $this->assertCount(2, $errors);

        $errors_for_foo1 = $list->getByKey('foo.foo1');
        $errors_for_foo2 = $list->getByKey('foo.foo2');
        $errors_for_foo3 = $list->getByKey('foo.*');
        $this->assertEquals('error_group1', $errors_for_foo1->getReadable());
        $this->assertEquals('error_group2', $errors_for_foo2->getReadable());
    }

  function testRenameFields()
  {
    $list = new lmbErrorList();

    $list->addError($message = '{Field_1} is field and {Field_2} also is a field',
      array('Field_1' => 'login', 'Field_2' => 'password'));
    $list->addError($message = "The greatest {Field_1}'s story is '{Field_2} and {Field_3}'",
      array('Field_1' => 'that man', 'Field_2' => 'that guy', 'Field_3' => 'that girl'));

    $new_field_names = array(
      'login' => 'custom_login',
      'password' => 'custom_password',
      'that man' => 'Shakespeare',
      'that guy' => 'Romeo',
      'that girl' => 'Juliet'
    );

    $list->renameFields($new_field_names);

    $errors = $list->export();

    $this->assertEquals(array('Field_1' => 'custom_login', 'Field_2' => 'custom_password'), $errors['login']['fields']);
    $this->assertEquals(array('Field_1' => 'Shakespeare', 'Field_2' => 'Romeo', 'Field_3' => 'Juliet'), $errors['that man']['fields']);
  }

  function testRenameFieldsWithSimilarNames() {
    $list = new lmbErrorList();

    $list->addError($message = '{Field_1} must repeat {Field_2}',
      array('Field_1' => 'passretype', 'Field_2' => 'pass'));

    $new_field_names = array(
      'passretype' => 'secondary typed password',
      'pass' => 'first typed password'
    );

    $list->renameFields($new_field_names);

    $errors = $list->export();

    $this->assertEquals(array('Field_1' => $new_field_names['passretype'], 'Field_2' => $new_field_names['pass']), $errors['passretype']['fields']);
  }

}
