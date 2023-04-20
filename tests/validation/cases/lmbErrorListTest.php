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

    $this->assertEquals(1, sizeof($errors));
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

        $this->assertEquals(2, sizeof($errors));

        $errors_for_foo = $list->getByKey('foo');
        $this->assertEquals('error_group', $errors_for_foo[0]->getReadable());
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

    $this->assertEquals(array('Field_1' => 'custom_login', 'Field_2' => 'custom_password'), $errors[0]['fields']);
    $this->assertEquals(array('Field_1' => 'Shakespeare', 'Field_2' => 'Romeo', 'Field_3' => 'Juliet'), $errors[1]['fields']);
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

    $this->assertEquals($errors[0]['fields'], array('Field_1' => $new_field_names['passretype'], 'Field_2' => $new_field_names['pass']));
  }


}
