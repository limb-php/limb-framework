<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\active_record\cases;

// This test ensures that old value object functionality is still supported.

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\src\LazyLessonForBCTestObject;
use tests\active_record\cases\src\LessonForBCTestObject;
use tests\active_record\cases\src\LessonWithNullObjectForBCTestObject;
use tests\active_record\cases\src\TestingValueObject;

class lmbARValueObjectBCTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lesson_for_test');

  function testNewObjectReturnsNullValueObjects()
  {
    $lesson = new LessonForBCTestObject();
    $this->assertNull($lesson->getDateStart());
    $this->assertNull($lesson->getDateEnd());
  }

  function testSaveLoadValueObjects()
  {
    $lesson = new LessonForBCTestObject();

    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson->save();

    $lesson2 = lmbActiveRecord :: findById(LessonForBCTestObject::class, $lesson->getId());
    $this->assertEquals($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEquals($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testGenericGetReturnsAlreadyExistingObject()
  {
    $lesson = new LessonForBCTestObject();

    $lesson->setDateStart(new TestingValueObject($v1 = time() - 100));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $this->assertEquals($lesson->get('date_start')->getValue(), $v1);
    $this->assertEquals($lesson->get('date_end')->getValue(), $v2);
  }

  function testLazyValueObjects()
  {
    $lesson = new LessonForBCTestObject();

    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson->save();

    $lesson2 = new LazyLessonForBCTestObject($lesson->getId());

    $this->assertEquals($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEquals($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testValueObjectsAreImportedAndExportedProperly()
  {
    $lesson = new LessonForBCTestObject();
    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson2 = new LessonForBCTestObject($lesson->export());

    $this->assertEquals($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEquals($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testImportValueObjectsAreImportedProperly()
  {
    $lesson = new LessonForBCTestObject();

    $imported = array(
      'date_start' => new TestingValueObject($v1 = time()),
      'date_end' => new TestingValueObject($v2 = (time() + 100))
    );

    $lesson->import($imported);

    $lesson2 = new LessonForBCTestObject($lesson->export());

    $this->assertEquals($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEquals($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testValueObjectsAreImportedNotFromObjects() {

    $lesson = new LessonForBCTestObject();

    $imported = array(
      'date_start' => time(),
      'date_end' => time() + 300
    );

    $lesson->import($imported);

    $lesson2 = new LessonForBCTestObject($lesson->export());

    $this->assertEquals($lesson2->getDateStart()->getValue(), $imported['date_start']);
    $this->assertEquals($lesson2->getDateEnd()->getValue(), $imported['date_end']);

  }

  function testAllowNullValuesForValuesObjects()
  {
    $lesson = new LessonForBCTestObject();
    $lesson->not_required_date = null;
    $this->assertNull($lesson->getNotRequiredDate());
  }

  function testGetDefaultObject()
  {
    $lesson = new LessonWithNullObjectForBCTestObject();
    $this->assertEquals($lesson->getNotRequiredDate()->getValue(), 'null');
    $lesson->not_required_date = new TestingValueObject('not_null');
    $this->assertEquals($lesson->getNotRequiredDate()->getValue(), 'not_null');
  }

  function testEmptyValueForValuesObjects()
  {
    $lesson = new LessonForBCTestObject();
    $lesson->not_required_date = '';
    $this->assertEquals($lesson->getNotRequiredDate(), '');

    $lesson->not_required_date = 0;
    $this->assertEquals($lesson->getNotRequiredDate(), 0);
  }

  function testProperWrapForScalrValueWhithNotRequiredFlagForValueObject()
  {
    $lesson = new LessonForBCTestObject();
    $lesson->not_required_date = 'test';
    $this->assertInstanceOf($lesson->getNotRequiredDate(), TestingValueObject::class);
  }
}
