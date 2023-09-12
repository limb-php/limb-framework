<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\src\CourseForTestObject;
use tests\active_record\cases\src\LectureForTestObject;
use tests\active_record\cases\src\MemberForTest;
use tests\active_record\cases\src\NameForAggregateTest;
use tests\active_record\cases\src\PersonForTestObject;
use tests\active_record\cases\src\SocialSecurityForTestObject;
use tests\active_record\cases\src\TestOneTableObject;
use tests\active_record\cases\src\TestOneTableObjectWithHooks;

class lmbARDirtyTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test', 'test_one_table_object', 'member_for_test');

  function testJustFoundObjectIsNotDirty()
  {
    $object = new TestOneTableObject();
    $object->setContent('test');
    $object->save();

    $object2 = lmbActiveRecord :: find(TestOneTableObject::class, $object->getId());
    $this->assertFalse($object2->isDirty());
  }

  function testJustLoadedByIdObjectIsNotDirty()
  {
    $object = new TestOneTableObject();
    $object->setContent('test');
    $object->save();

    $object2 = new TestOneTableObject($object->getId());
    $this->assertFalse($object2->isDirty());
  }

  function testMarkDirty()
  {
    $object = new TestOneTableObject();
    $this->assertFalse($object->isDirty());
    $object->markDirty();
    $this->assertTrue($object->isDirty());
  }

  function testObjectBecomesDirtyIfAttributeIsSetWithSetter()
  {
    $object = new TestOneTableObject();
    $this->assertFalse($object->isDirty());
    $object->setContent('hey');
    $this->assertTrue($object->isDirty());
  }

  function testDirtyObjectBecomesCleanOnceSaved()
  {
    $object = new TestOneTableObject();
    $object->setContent('whatever');
    $this->assertTrue($object->isDirty());
    $object->save();
    $this->assertFalse($object->isDirty());
  }

  function testNonDirtyObjectIsNotUpdated()
  {
    $object = new TestOneTableObjectWithHooks();
    $object->setContent('whatever');

    ob_start();
    $object->save();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEquals($str, '|on_before_save||on_before_create||on_validate||on_save||on_create||on_after_create||on_after_save|');

    ob_start();
    $object->save();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEquals($str, '|on_before_save||on_after_save|');
  }

  function testUpdateOnlyDirtyFieldsInDbForNotNewObject()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation('some annotation');
    $object->setContent($initial_content = 'some content');
    $object->save();

    $object->setAnnotation('some other annotation');
    $object->setContent('some other content');

    $object->resetPropertyDirtiness('content'); // suppose we don't want to save this field

    $object->save();

    $loaded_object = lmbActiveRecord :: findById(TestOneTableObject::class, $object->getId());
    $this->assertEquals($loaded_object->getAnnotation(), $object->getAnnotation());
    $this->assertEquals($loaded_object->getContent(), $initial_content);
  }

  function testUpdateWhileNoDirtyFields()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation($initial_annotation = 'some annotation');
    $object->setContent($initial_content = 'some content');
    $object->save();

    $object->setAnnotation('some other annotation');
    $object->setContent('some other content');

    $object->resetPropertyDirtiness('content');
    $object->resetPropertyDirtiness('annotation');

    $object->save();

    $loaded_object = lmbActiveRecord :: findById(TestOneTableObject::class, $object->getId());
    $this->assertEquals($loaded_object->getAnnotation(), $initial_annotation);
    $this->assertEquals($loaded_object->getContent(), $initial_content);
  }

  function testSettingSameTablePropertyValueDoesntMakeObjectDirty()
  {
    $object = new TestOneTableObject();
    $object->setContent('whatever');
    $object->save();
    $this->assertFalse($object->isDirty());

    $object->setContent($object->getContent());
    $this->assertFalse($object->isDirty());

    $object->setContent('whatever else');
    $this->assertTrue($object->isDirty());
  }

  function testSettingNewParentObjectDoesntMakeNewObjectDirty()
  {
    $course = new CourseForTestObject();

    $lecture = new LectureForTestObject();
    $lecture->setCourse($course);

    $this->assertTrue($lecture->isNew());
    $this->assertFalse($lecture->isDirty());
  }

  function testParentIsSavedEvenForCleanObject()
  {
    $course = new CourseForTestObject();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTestObject();
    $lecture->setCourse($course);
    $lecture->save();

    $lecture2 = new LectureForTestObject($lecture->getId());
    $this->assertEquals($lecture2->getCourse()->getTitle(), 'course');
  }

  function testChangingSavedParentObjectDoesntMakeObjectDirty()
  {
    $course = new CourseForTestObject();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTestObject();
    $lecture->setCourse($course);
    $lecture->save();

    $lecture2 = new LectureForTestObject($lecture->getId());
    $this->assertFalse($lecture2->isDirty());

    $course2 = $lecture2->getCourse();

    $course2->setTitle('changed_course');
    $this->assertFalse($lecture2->isDirty());
  }

  function testSettingExistingParentMakesNewObjectDirty()
  {
    $course = new CourseForTestObject();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTestObject();
    $lecture->setCourse($course);
    $this->assertTrue($lecture->isDirty());
    $lecture->save();

    $lecture2 = new LectureForTestObject($lecture->getId());
    $this->assertEquals($lecture2->getCourse()->getTitle(), $course->getTitle());
  }

  function testSettingExistingParentMakesExistingObjectDirty()
  {
    $course = new CourseForTestObject();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTestObject();
    $lecture->setTitle('test');
    $lecture->save();

    $lecture->setCourse($course);
    $this->assertTrue($lecture->isDirty());
    $lecture->save();

    $lecture2 = new LectureForTestObject($lecture->getId());
    $this->assertEquals($lecture2->getCourse()->getTitle(), $course->getTitle());
  }

  function testAddingToCollectionDoesntMakeNewObjectDirty()
  {
    $course = new CourseForTestObject();

    $lecture = new LectureForTestObject();

    $course->addToLectures($lecture);
    $this->assertFalse($course->isDirty());
  }

  function testAddingToCollectionDoesntMakeExistingObjectDirty()
  {
    $course = new CourseForTestObject();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTestObject();

    $course->addToLectures($lecture);
    $this->assertFalse($course->isDirty());
  }

  function testGettingCollectionDoesntMakeObjectDirty()
  {
    $course = new CourseForTestObject();
    $lectures = $course->getLectures();
    $this->assertFalse($course->isDirty());
  }

  function testSettingAggregatedObjectDoesNotMakesObjectDirty()
  {
    $member = new MemberForTest();

    $member->setName(new NameForAggregateTest());
    $this->assertFalse($member->isDirty());
  }

  function testAggregatedObjectFieldsAreCheckedForDirtinessOnSaveOnly()
  {
    $name = new NameForAggregateTest();
    $name->setFirst('name');

    $member = new MemberForTest();
    $member->setName($name);
    $member->save();

    $member2 = new MemberForTest($member->getId());
    $this->assertFalse($member->isDirty());

    $member2->getName()->setFirst('other name');
    $this->assertFalse($member2->isDirty());
    $member2->save();

    $member3 = new MemberForTest($member->getId());
    $this->assertEquals($member3->getName()->getFirst(), 'other name');
  }

  function testUnsettingOneToOneChildObjectMakesPropertyDirty()
  {
    $person = new PersonForTestObject();
    $person->setName('Jim');
    $number = new SocialSecurityForTestObject();
    $number->setCode('099123');

    $person->setSocialSecurity($number);
    $person->save();

    $person->setSocialSecurity(null);
    $this->assertTrue($person->isDirtyProperty('social_security'));
  }
}
