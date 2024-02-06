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
use limb\core\src\lmbSet;
use Tests\active_record\cases\src\CourseForTestObject;
use Tests\active_record\cases\src\GroupForTestObject;
use Tests\active_record\cases\src\LazyTestOneTableObject;
use Tests\active_record\cases\src\LectureForTestObject;
use Tests\active_record\cases\src\MemberForTest;
use Tests\active_record\cases\src\PersonForTestObject;
use Tests\active_record\cases\src\SocialSecurityForTestObject;
use Tests\active_record\cases\src\TestOneTableObject;
use Tests\active_record\cases\src\UserForTestObject;

require_once '.setup.php';
class LessonForTestWithCustomImport extends lmbActiveRecord
{
    protected $_db_table_name = 'lesson_for_test';
    public $echo_on_after_import;

    function _onAfterImport()
    {
        if ($this->echo_on_after_import)
            echo $this->echo_on_after_import;
    }
}

class lmbARImportTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test', 'test_one_table_object',
        'user_for_test', 'group_for_test', 'user_for_test2group_for_test',
        'person_for_test', 'social_security_for_test', 'lesson_for_test', 'member_for_test');

    function testImportingObjectCallsItsExportMethod()
    {
        $object = new TestOneTableObject();
        $object->import(new lmbSet(array('annotation' => 'Some annotation')));
        $this->assertEquals($object->getAnnotation(), 'Some annotation');
    }

    function testImportNewActiveRecord()
    {
        $object1 = new TestOneTableObject();
        $object1->setId(100); //note this
        $object1->setAnnotation($annotation = 'Some annotation');

        $object2 = new TestOneTableObject();
        $object2->import($object1);
        $this->assertEquals($object2->getId(), $object1->getId());
        $this->assertEquals($object2->getAnnotation(), $annotation);
        $this->assertTrue($object2->isNew());
        $this->assertTrue($object2->isDirty());
    }

    function testImportExistingActiveRecord()
    {
        $object1 = new TestOneTableObject();
        $object1->setAnnotation($annotation = 'Some annotation');
        $object1->save();

        $object2 = new TestOneTableObject();
        $object2->import($object1);
        $this->assertEquals($object2->getId(), $object1->getId());
        $this->assertEquals($object2->getAnnotation(), $annotation);
        $this->assertFalse($object2->isNew());
        $this->assertTrue($object2->isDirty());
    }

    function testPassingActiveRecordToConstructorCallsImport()
    {
        $object1 = new TestOneTableObject();
        $object1->setAnnotation($annotation = 'Some annotation');
        $object1->save();

        $object2 = new TestOneTableObject($object1);
        $this->assertEquals($object2->getId(), $object1->getId());
        $this->assertEquals($object2->getAnnotation(), $annotation);
        $this->assertFalse($object2->isNew());
        $this->assertTrue($object2->isDirty());
    }

    function testImportActiveRecordWithLazyAttributes()
    {
        $object = new LazyTestOneTableObject();
        $object->setContent($content = 'Some content');
        $object->setAnnotation($annotation = 'Some annotation');
        $object->save();

        $object1 = new LazyTestOneTableObject();
        $object2 = new LazyTestOneTableObject($object->getId());

        $object1->import($object2);
        $this->assertFalse(array_key_exists('annotation', $object1->exportRaw()));
        $this->assertTrue($object1->has('annotation'));
        $this->assertEquals($object1->getAnnotation(), $annotation);
        $this->assertTrue(array_key_exists('annotation', $object1->exportRaw()));
        $this->assertFalse(array_key_exists('content', $object1->exportRaw()));
        $this->assertTrue($object1->has('content'));
        $this->assertEquals($object1->getContent(), $content);
        $this->assertTrue(array_key_exists('content', $object1->exportRaw()));
    }

    function testImportOverwritesIdOfNewObject()
    {
        $object = new TestOneTableObject();
        $object->setId(1);

        $source = array('id' => 1000,
            'annotation' => 'Some annotation',
            'content' => 'Some content',
        );

        $object->import($source);
        $this->assertEquals($object->getId(), 1000);
        $this->assertEquals($object->getAnnotation(), 'Some annotation');
        $this->assertEquals($object->getContent(), 'Some content');
    }

    function testImportPreservesIdOfExistingObject()
    {
        $object = new TestOneTableObject();
        $object->setAnnotation('Initial annotation');
        $object->save();
        $id = $object->getId();

        $source = array('id' => 10000,
            'annotation' => 'Some annotation',
            'content' => 'Some content',
        );

        $object->import($source);
        $this->assertEquals($object->getId(), $id);
        $this->assertNotEquals($object->getId(), 1000);// just one extra check
        $this->assertEquals($object->getAnnotation(), 'Some annotation');
        $this->assertEquals($object->getContent(), 'Some content');
    }

    function testPassingArrayToConstructorCallsImport()
    {
        $source = array('id' => 1000,
            'annotation' => 'Some annotation',
            'content' => 'Some content',
        );

        $object = new TestOneTableObject($source);
        $this->assertEquals($object->getId(), 1000);
        $this->assertEquals($object->getAnnotation(), 'Some annotation');
        $this->assertEquals($object->getContent(), 'Some content');
    }

    function testImportWhereOne2ManyCollectionIsArrayOfIds()
    {
        $course = new CourseForTestObject();
        $course->setTitle('Some course');

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $source = array('title' => $course->getTitle(),
            'lectures' => array($l1->getId(), $l2->getId()));

        $course2 = new CourseForTestObject();
        $course2->import($source);
        $this->assertEquals($course2->getTitle(), $course->getTitle());
        $this->assertEquals(2, $course2->getLectures()->count());
        $this->assertEquals($course2->getLectures()->at(0)->getTitle(), $l1->getTitle());
        $this->assertEquals($course2->getLectures()->at(1)->getTitle(), $l2->getTitle());
    }

    function testImportWhereOne2ManyCollectionIsMixedArray()
    {
        $course = new CourseForTestObject();
        $course->setTitle('Some course');

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $source = array('title' => $course->getTitle(),
            'lectures' => array($l1->getId(), $l2));

        $course2 = new CourseForTestObject();
        $course2->import($source);
        $this->assertEquals($course2->getTitle(), $course->getTitle());
        $this->assertEquals(2, $course2->getLectures()->count());
        $this->assertEquals($course2->getLectures()->at(0)->getTitle(), $l1->getTitle());
        $this->assertEquals($course2->getLectures()->at(1)->getTitle(), $l2->getTitle());
    }

    function testImportResetsExistingOne2ManyCollection()
    {
        $course = new CourseForTestObject();
        $course->setTitle('Some course');

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $source = array('title' => $course->getTitle(),
            'lectures' => array($l2->getId()));

        $course2 = new CourseForTestObject($course->getId());

        $course2->import($source);
        $this->assertEquals($course2->getTitle(), $course->getTitle());
        $this->assertEquals(1, $course2->getLectures()->count());
        $this->assertEquals($course2->getLectures()->at(0)->getTitle(), $l2->getTitle());
    }

    function testImportResetsExistingMany2ManyCollection()
    {
        $group = new GroupForTestObject();
        $group->setTitle('Some group');

        $u1 = new UserForTestObject();
        $u1->setFirstName('Bob');
        $u2 = new UserForTestObject();
        $u2->setFirstName('John');

        $group->addToUsers($u1);
        $group->addToUsers($u2);

        $group->save();

        $source = array('title' => $group->getTitle(),
            'users' => array($u2->getId()));

        $group2 = new GroupForTestObject($group->getId());
        $group2->import($source);
        $this->assertEquals($group2->getTitle(), $group->getTitle());
        $this->assertEquals($group2->getUsers()->count(), 1);
        $this->assertEquals($group2->getUsers()->at(0)->getFirstName(), $u2->getFirstName());
    }

    function testImportWhereOne2ManyParentIsNumericId()
    {
        $course = new CourseForTestObject();
        $course->setTitle('Some course');

        $l = new LectureForTestObject();
        $l->setTitle('Physics');
        $l->setCourse($course);

        $l->save();

        $source = array('title' => $l->getTitle(),
            'course' => $course->getId());

        $l2 = new LectureForTestObject();
        $l2->import($source);

        $this->assertEquals($l2->getTitle(), $l->getTitle());
        $this->assertEquals($l2->getCourse()->getTitle(), $course->getTitle());
    }

    function testImportWhereOne2ManyParentIsObject()
    {
        $course = new CourseForTestObject();
        $course->setTitle('Some course');

        $l = new LectureForTestObject();
        $l->setTitle('Physics');
        $l->setCourse($course);

        $l->save();

        $source = array('title' => $l->getTitle(),
            'course' => $course);

        $l2 = new LectureForTestObject();
        $l2->import($source);
        $this->assertEquals($l2->getTitle(), $l->getTitle());
        $this->assertEquals($l2->getCourse()->getTitle(), $course->getTitle());
    }

    function testImportWhereMany2ManyCollectionIsArrayOfIds()
    {
        $user1 = new UserForTestObject();
        $user1->setFirstName('Bob');

        $g1 = new GroupForTestObject();
        $g1->setTitle('vp1');
        $g2 = new GroupForTestObject();
        $g2->setTitle('vp1');

        $user1->addToGroups($g1);
        $user1->addToGroups($g2);
        $user1->save();

        $source = array('first_name' => $user1->getFirstName(),
            'groups' => array($g1->getId(), $g2->getId()));

        $user2 = new UserForTestObject();
        $user2->import($source);
        $this->assertEquals($user2->getFirstName(), $user1->getFirstName());
        $this->assertEquals(2, $user2->getGroups()->count());
        $this->assertEquals($user2->getGroups()->at(0)->getTitle(), $g1->getTitle());
        $this->assertEquals($user2->getGroups()->at(1)->getTitle(), $g2->getTitle());
    }

    function testImportWhereMany2ManyCollectionIsMixedArray()
    {
        $user1 = new UserForTestObject();
        $user1->setFirstName('Bob');

        $g1 = new GroupForTestObject();
        $g1->setTitle('vp1');
        $g2 = new GroupForTestObject();
        $g2->setTitle('vp1');

        $user1->addToGroups($g1);
        $user1->addToGroups($g2);
        $user1->save();

        $source = array('first_name' => $user1->getFirstName(),
            'groups' => array($g1->getId(), $g2));

        $user2 = new UserForTestObject();
        $user2->import($source);
        $this->assertEquals($user2->getFirstName(), $user1->getFirstName());
        $this->assertEquals(2, $user2->getGroups()->count());
        $this->assertEquals($user2->getGroups()->at(0)->getTitle(), $g1->getTitle());
        $this->assertEquals($user2->getGroups()->at(1)->getTitle(), $g2->getTitle());
    }

    function testImportOne2OneWhereParentIsNumericId()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');
        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('code' => $number->getCode(),
            'person' => $person->getId());

        $number2 = new SocialSecurityForTestObject();
        $number2->import($source);
        $this->assertEquals($number2->getCode(), $number->getCode());
        $this->assertEquals($number2->getPerson()->getName(), $person->getName());
    }

    function testImportOne2OneWhereParentIsObject()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');
        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('code' => $number->getCode(),
            'person' => $person);

        $number2 = new SocialSecurityForTestObject();
        $number2->import($source);
        $this->assertEquals($number2->getCode(), $number->getCode());
        $this->assertEquals($number2->getPerson()->getName(), $person->getName());
    }

    function testImportOne2OneWhereChildIsId()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');
        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('name' => $person->getName(),
            'social_security' => $number->getId());

        $person2 = new PersonForTestObject();
        $person2->import($source);
        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertEquals($person2->getSocialSecurity()->getCode(), $number->getCode());
    }

    function testImportOne2OneWhereChildIsObject()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');
        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('name' => $person->getName(),
            'social_security' => $number);

        $person2 = new PersonForTestObject();
        $person2->import($source);
        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertEquals($person2->getSocialSecurity()->getCode(), $number->getCode());
    }

    function testImportNullEntity()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');
        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('name' => $person->getName(),
            'social_security' => null);

        $person2 = clone $person;
        $person2->import($source);
        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertNull($person2->getSocialSecurity());
    }

    function testImportNullEntityString()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');
        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('name' => $person->getName(),
            'social_security' => 'null');

        $person2 = clone $person;
        $person2->import($source);
        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertNull($person2->getSocialSecurity());
    }

    function testImportNullEntityEmptyString()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');
        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('name' => $person->getName(),
            'social_security' => '');

        $person2 = clone $person;
        $person2->import($source);
        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertNull($person2->getSocialSecurity());
    }

    function testImportWithAggrigatedObject()
    {
        $member = new MemberForTest();

        $member->import(array('first_name' => $first = 'first_name',
            'last_name' => $last = 'last_name'));

        $this->assertEquals($member->getName()->getFirst(), $first);
        $this->assertEquals($member->getName()->getLast(), $last);
    }

    function testOnAfterImport()
    {
        $lesson = new LessonForTestWithCustomImport();
        $lesson->echo_on_after_import = 'Halo!';

        ob_start();
        $lesson->import(array('date_start' => 100));
        $what = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('Halo!', $what);
    }
}
