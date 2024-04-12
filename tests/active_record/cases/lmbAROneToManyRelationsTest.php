<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\active_record\cases;

use limb\active_record\src\lmbAROneToManyCollection;
use limb\active_record\src\lmbActiveRecord;
use limb\active_record\src\lmbARNotFoundException;
use limb\active_record\src\lmbARException;
use limb\core\src\exception\lmbException;
use limb\validation\src\lmbErrorList;
use limb\validation\src\lmbValidator;
use tests\active_record\cases\src\CourseForTestObject;
use tests\active_record\cases\src\CourseForTestWithCustomCollection;
use tests\active_record\cases\src\CourseForTestWithNullifyRelationProperty;
use tests\active_record\cases\src\CourseWithNullableLectures;
use tests\active_record\cases\src\LectureForTestObject;
use tests\active_record\cases\src\LectureIndependentFromCourse;
use tests\active_record\cases\src\LecturesForTestCollectionStub;
use tests\active_record\cases\src\ProgramForTestObject;

class lmbAROneToManyRelationsTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('course_for_test', 'lecture_for_test');

    function testHas()
    {
        $lecture = new LectureForTestObject();
        $this->assertTrue(isset($lecture['course']));
    }

    function testMapPropertyToField()
    {
        $course = new CourseForTestObject();
        $this->assertEquals('lectures', $course->mapFieldToProperty('course_id'));
        $this->assertNull($course->mapFieldToProperty('blah'));

        $lecture = new LectureForTestObject();
        $this->assertEquals('course', $lecture->mapFieldToProperty('course_id'));
        $this->assertNull($lecture->mapFieldToProperty('blah'));
    }

    function testNewObjectReturnsEmptyCollection()
    {
        $course = new CourseForTestObject();
        $lectures = $course->getLectures();
        $lectures->rewind();
        $this->assertFalse($lectures->valid());
    }

    function testNewObjectReturnsNullParent()
    {
        $lecture = new LectureForTestObject();
        $this->assertNull($lecture->getCourse());
    }

    function testAddToCollection()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $rs = $course->getLectures();

        $rs->rewind();
        $this->assertEquals($rs->current()->getTitle(), $l1->getTitle());
        $rs->next();
        $this->assertEquals($rs->current()->getTitle(), $l2->getTitle());
    }

    function testSetingCollectionDirectlyCallsAddToMethod()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->setLectures(array($l1, $l2));
        $lectures = $course->getLectures();
        $this->assertEquals(2, sizeof($lectures));
        $this->assertEquals($lectures[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($lectures[1]->getTitle(), $l2->getTitle());
    }

    function testSetFlushesPreviousCollection()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->setLectures(array($l1));
        $lectures = $course->getLectures()->getArray();
        $this->assertEquals($lectures[0]->getTitle(), $l1->getTitle());
        $this->assertEquals(1, sizeof($lectures));
    }

    function testSaveCollection()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $course2 = lmbActiveRecord:: findById(CourseForTestObject::class, $course->getId());
        $rs = $course2->getLectures();

        $rs->rewind();
        $this->assertEquals($rs->current()->getTitle(), $l1->getTitle());
        $rs->next();
        $this->assertEquals($rs->current()->getTitle(), $l2->getTitle());
    }

    function testGenericGetLoadsCollection()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $course2 = lmbActiveRecord:: findById(CourseForTestObject::class, $course->getId());
        $rs = $course2->get('lectures');

        $rs->rewind();
        $this->assertEquals($rs->current()->getTitle(), $l1->getTitle());
        $rs->next();
        $this->assertEquals($rs->current()->getTitle(), $l2->getTitle());
    }

    function testParentObjectCanBeNull()
    {
        $course = $this->_initCourse();

        $lecture = new LectureForTestObject();
        $lecture->setTitle('Physics');
        $lecture->setCourse($course);
        $lecture->save();

        $lecture2 = lmbActiveRecord:: findById(LectureForTestObject::class, $lecture->getId());
        $this->assertEquals($lecture2->getCourse()->getTitle(), $course->getTitle());
        $this->assertNull($lecture2->getAltCourse());

        $lecture2->setAltCourse($course);
        $lecture2->save();

        $lecture3 = lmbActiveRecord:: findById(LectureForTestObject::class, $lecture2->getId());
        $this->assertEquals($lecture3->getCourse()->getTitle(), $course->getTitle());
        $this->assertEquals($lecture3->getAltCourse()->getTitle(), $course->getTitle());
    }

    function testLoadingNonExistingParentThrowsExceptionByDefault()
    {
        $course = $this->_initCourse();

        $lecture = new LectureForTestObject();
        $lecture->setTitle('Physics');
        $lecture->setCourse($course);
        $lecture->save();

        $this->db->delete('course_for_test', 'id = ' . $course->getId());

        $lecture2 = lmbActiveRecord:: findById(LectureForTestObject::class, $lecture->getId());
        try {
            $lecture2->getCourse();
            $this->assertTrue(false);
        } catch (lmbARNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    function testLoadingNonExistingParent_NOT_ThrowsException_IfSpecialFlagUsedForRelationDefinition()
    {
        $course = $this->_initCourse();

        $lecture = new LectureIndependentFromCourse();
        $lecture->setTitle('Physics');
        $lecture->setCourse($course);
        $lecture->save();

        $this->db->delete('course_for_test', 'id = ' . $course->getId());

        $lecture2 = lmbActiveRecord::findById(LectureIndependentFromCourse::class, $lecture->getId());
        $this->assertNull($lecture2->getCourse());
    }

    function testSettingNullParentObject()
    {
        $course = $this->_initCourse();

        $lecture = new LectureForTestObject();
        $lecture->setTitle('Physics');
        $lecture->setAltCourse($course);
        $lecture->save();

        $this->assertEquals(1, $course->getAltLectures()->count());

        $lecture2 = lmbActiveRecord::findById(LectureForTestObject::class, $lecture->getId());
        $lecture2->setAltCourse(null);
        // $lecture2->setAltCourse(false);
        $this->assertTrue($lecture2->isDirtyProperty('alt_course'));
        //$this->assertTrue($lecture2->isDirtyProperty('alt_course_id'));
        $lecture2->save();

        $this->assertEquals(0, $course->getAltLectures()->count());

        $lecture3 = lmbActiveRecord::findById(LectureForTestObject::class, $lecture2->getId());
        $this->assertNull($lecture3->getAltCourse());
    }

    function testSavingChildForExistingParentDoesntSaveParent()
    {
        $course = $this->_initCourse();

        $this->assertEquals(0, $course->save_calls);

        $course->save();

        $this->assertEquals(1, $course->save_calls);

        $lecture = new LectureForTestObject();
        $lecture->setTitle('Physics');
        $lecture->setAltCourse($course);
        $lecture->save();

        $this->assertEquals($course->save_calls, 1);
    }

    function testChangingParentIdRelationFieldDirectly()
    {
        $course1 = $this->_initCourse();
        $course1->save();

        $course2 = $this->_initCourse();
        $course2->save();

        $lecture = new LectureForTestObject();
        $lecture->setTitle('Physics');
        $lecture->setCourse($course1);
        $lecture->save();

        $lecture2 = new LectureForTestObject($lecture->getId());
        $this->assertEquals($lecture2->getCourse()->getId(), $course1->getId());

        $lecture2->set('course_id', $course2->getId());
        $lecture2->save();

        $lecture3 = new LectureForTestObject($lecture->getId());
        $this->assertEquals($lecture3->getCourse()->getId(), $course2->getId());
    }

    function testChangingParentIdRelationFieldDirectlyDoesNotWorkIfParentObjectIsDirty()
    {
        $course1 = $this->_initCourse();
        $course1->save();

        $course2 = $this->_initCourse();
        $course2->save();

        $lecture = new LectureForTestObject();
        $lecture->setTitle('Physics');
        $lecture->setCourse($course1);
        $lecture->save();

        $lecture2 = new LectureForTestObject($lecture->getId());
        $this->assertEquals($lecture2->getCourse()->getId(), $course1->getId());

        $lecture2->set('course_id', $course2->getId());
        $lecture2->setCourse($course1);
        $lecture2->save();

        $lecture3 = new LectureForTestObject($lecture->getId());
        $this->assertEquals($lecture3->getCourse()->getId(), $course1->getId());
    }

    function testOwnerSetAutomaticallyForChildAddedToCollection()
    {
        $course = $this->_initCourse();

        $lecture = new LectureForTestObject();
        $lecture->setTitle('Physics');

        $course->getLectures()->add($lecture);

        $this->assertEquals($lecture->getCourse(), $course);
    }

    function testDeleteCollection()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $course2 = lmbActiveRecord:: findById(CourseForTestObject::class, $course->getId());
        $course2->destroy();

        $this->assertNull(lmbActiveRecord:: findFirst(LectureForTestObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $l1->getId())));
        $this->assertNull(lmbActiveRecord:: findFirst(LectureForTestObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $l2->getId())));
    }

    function testNullifyOnDestroy()
    {
        $course = new CourseForTestWithNullifyRelationProperty();
        $course->setTitle('Super course');

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $course2 = new CourseForTestWithNullifyRelationProperty($course->getId());
        $course2->destroy();

        $lectures = lmbActiveRecord:: find(LectureForTestObject::class)->getArray();
        $this->assertEquals(sizeof($lectures), 2);
        $this->assertNull($lectures[0]->getCourseId());
        $this->assertNull($lectures[0]->getCourseId());
    }

    function testUseCustomCollection()
    {
        $course = new CourseForTestWithCustomCollection();
        $this->assertTrue($course->getLectures() instanceof LecturesForTestCollectionStub);
    }

    function testSetFlushesPreviousCollectionInDatabaseToo()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $course2 = lmbActiveRecord:: findById(CourseForTestObject::class, $course->getId());

        $l3 = new LectureForTestObject();
        $l3->setTitle('Math');

        $course2->setLectures(array($l3));
        $course2->save();

        $course3 = lmbActiveRecord:: findById(CourseForTestObject::class, $course->getId());

        $lectures = $course3->getLectures();
        $this->assertEquals($lectures->count(), 1);
        $this->assertEquals($lectures->at(0)->getTitle(), $l3->getTitle());
    }

    function testErrorListIsSharedWithCollection()
    {
        $course = $this->_initCourse();

        $l = new LectureForTestObject();
        $validator = new lmbValidator();
        $validator->addRequiredRule('title');
        $l->setValidator($validator);

        $course->addToLectures($l);

        $error_list = new lmbErrorList();
        $this->assertFalse($course->trySave($error_list));
    }

    function testFetchWithRelatedObjects_UsingJoinMethod()
    {
        $course = $this->creator->createCourse();

        $alt_course1 = $this->creator->createCourse();
        $alt_course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course, $alt_course1);
        $lecture2 = $this->creator->createLecture($course, $alt_course2);
        $lecture3 = $this->creator->createLecture($course, $alt_course1);

        $lectures = $course->getLectures()->join('course')->join('alt_course');
        $arr = $lectures->getArray();

        //make sure we really eager fetching
        $this->db->delete('course_for_test');

        $this->assertInstanceOf(LectureForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $lecture1->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]->getCourse());
        $this->assertEquals($arr[0]->getCourse()->getTitle(), $course->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]->getAltCourse());
        $this->assertEquals($arr[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

        $this->assertInstanceOf(LectureForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getTitle(), $lecture2->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]->getCourse());
        $this->assertEquals($arr[1]->getCourse()->getTitle(), $course->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]->getAltCourse());
        $this->assertEquals($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());

        $this->assertInstanceOf(LectureForTestObject::class, $arr[2]);
        $this->assertEquals($arr[2]->getTitle(), $lecture3->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[2]->getCourse());
        $this->assertEquals($arr[2]->getCourse()->getTitle(), $course->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[2]->getAltCourse());
        $this->assertEquals($arr[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    }

    function testFetchFirstWithRelationObjectsUsingAttach_AndThenSave()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course1);
        $lecture2 = $this->creator->createLecture($course2);
        $lecture3 = $this->creator->createLecture($course2);

        $course2_loaded = lmbActiveRecord::findFirst(CourseForTestObject::class, array('criteria' => 'course_for_test.id = ' . $course2->getId(), 'attach' => 'lectures'));

        $course2_loaded->setTitle('Some other title');

        $course2_loaded->save();

        $course2_loaded2 = lmbActiveRecord::findFirst(CourseForTestObject::class, array('criteria' => 'course_for_test.id = ' . $course2->getId(), 'attach' => 'lectures'));
        $lectures = $course2_loaded2->getLectures();
        $this->assertEquals(2, count($lectures));
    }

    function testImportAndSaveNullableRelataions()
    {
        $course = new CourseWithNullableLectures();
        $course->setTitle("Title");
        $lecture1 = new LectureIndependentFromCourse();
        $lecture1->setTitle("Lecture 1");
        $lecture2 = new LectureIndependentFromCourse();
        $lecture2->setTitle("Lecture 2");
        $lecture3 = new LectureIndependentFromCourse();
        $lecture3->setTitle("Lecture 3");
        $course->setLectures(array($lecture1, $lecture2, $lecture3));
        $course->save();
        $this->assertEquals(3, lmbActiveRecord::find(LectureForTestObject::class)->count());

        $course_arr = $course->export();
        $lect_arr = $course->getLectures()->getIds();
        array_pop($lect_arr);
        $course_arr['lectures'] = $lect_arr;
        $course->import($course_arr);
        $course->save();
        $this->assertEquals(3, lmbActiveRecord::find(LectureForTestObject::class)->count());
    }

    function testSwapNullableRelations()
    {
        $course1 = new CourseWithNullableLectures();
        $lectA = new LectureIndependentFromCourse();
        $lectA->setTitle("Lecture A");
        $lectB = new LectureIndependentFromCourse();
        $lectB->setTitle("Lecture B");
        $course1->setLectures(array($lectA, $lectB));
        $course1->setTitle("Course 1");
        $course2 = new CourseWithNullableLectures();
        $lectC = new LectureIndependentFromCourse();
        $lectC->setTitle("Lecture C");
        $lectD = new LectureIndependentFromCourse();
        $lectD->setTitle("Lecture D");
        $course2->setLectures(array($lectC, $lectD));
        $course2->setTitle("Course 2");

        $course1->save();
        $course2->save();
        $c1 = $course1->export();
        $c2 = $course2->export();
        $c1['lectures'] = $course2->getLectures()->getIds();
        $c2['lectures'] = $course1->getLectures()->getIds();

        try {
            $course1->import($c1);
            $course1->save();
            $course2 = new CourseWithNullableLectures($course2->getId());
            $course2->import($c2);
            $c2 = $course2->save();
        } catch (lmbARException $e) {

        }
        $this->assertEquals(4, lmbActiveRecord::find(LectureForTestObject::class)->count());
    }

    function _initCourse()
    {
        $course = new CourseForTestObject();
        $course->setTitle('Course' . mt_rand());
        return $course;
    }

    function testCorrectUsageCrossRelations()
    {
        $program = new ProgramForTestObject();
        $program->setTitle('Program');
        $program->save();

        $course = new CourseForTestObject();
        $course->setProgram($program);
        $course->save();

        $lecture = new LectureForTestObject();
        $lecture->setCourse($course);
        $lecture->setCachedProgram($program);
        $lecture->save();

        try {
            $finded_lectures = $program->getCachedLectures()->find(array(
                'join' => array('course'),
            ))->getArray();

            $this->assertTrue(true);
        } catch (lmbException $e) {
            $this->fail();
        }

    }
}
