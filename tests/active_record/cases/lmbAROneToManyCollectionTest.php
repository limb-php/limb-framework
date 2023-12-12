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
use limb\active_record\src\lmbAROneToManyCollection;
use limb\core\src\exception\lmbException;
use Tests\active_record\cases\src\CourseForTestObject;
use Tests\active_record\cases\src\LectureForTestObject;
use Tests\active_record\cases\src\LectureForTestObjectStub;
use Tests\active_record\cases\src\lmbARTestingDSDecorator;
use Tests\active_record\cases\src\SpecialCourseForTestObject;
use Tests\active_record\cases\src\SpecialLectureForTestObject;
use Tests\active_record\cases\src\VerySpecialCourseForTestObject;

class lmbAROneToManyCollectionTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test');

    function testAddToWithExistingOwner()
    {
        $course = $this->_createCourseAndSave();

        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);

        $arr = $collection->getArray();

        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
        $this->assertEquals(2, sizeof($arr));

        $collection2 = new lmbAROneToManyCollection('lectures', $course);
        $arr = $collection2->getArray();

        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
        $this->assertEquals(2, sizeof($arr));
    }

    function testResetCollectionOnAddForExistingOwner()
    {
        $course = $this->_createCourseAndSave();

        $lectures = $course->getLectures();
        $lectures->rewind();
        $this->assertFalse($lectures->valid());

        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $lectures = $course->getLectures();
        $lectures->rewind();
        $lecture = $lectures->current();
        $this->assertEquals($lecture->getTitle(), $l1->getTitle());
    }

    function testAddToWithNonSavedOwner()
    {
        $course = $this->_createCourse();

        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);

        $arr = $collection->getArray();
        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());

        $collection2 = new lmbAROneToManyCollection('lectures', $course);
        $arr = $collection2->getArray();

        $this->assertEquals(0, sizeof($arr));
    }

    function testSaveWithExistingOwnerDoesNothing()
    {
        $l1 = $this->createMock(LectureForTestObject::class);
        $l2 = $this->createMock(LectureForTestObject::class);

        $course = $this->_createCourseAndSave();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);

        $l1->expects($this->never())->method('save');
        $l2->expects($this->never())->method('save');

        $collection->save();
    }

    function testSaveWithNonSavedOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = $this->_createCourse();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);

        $collection2 = new lmbAROneToManyCollection('lectures', $course);
        $this->assertEquals(0, sizeof($collection2->getArray()));

        $course->save();
        $collection->save();

        $collection3 = new lmbAROneToManyCollection('lectures', $course);
        $arr = $collection3->getArray();
        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
    }

    function testSavingOwnerDoesntAffectCollection()
    {
        $l1 = new LectureForTestObjectStub();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObjectStub();
        $l2->setTitle('Math');

        $course = $this->_createCourse();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);

        $course->save();

        $collection->add($l2);

        //items in memory
        $arr = $collection->getArray();
        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
        $this->assertEquals(0, $l1->save_calls);
        $this->assertEquals(0, $l2->save_calls);

        //...and not db yet
        $collection2 = new lmbAROneToManyCollection('lectures', $course);
        $this->assertEquals(0, sizeof($collection2->getArray()));

        $collection->save();

        $collection3 = new lmbAROneToManyCollection('lectures', $course);
        $arr = $collection3->getArray();
        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());

        //check items not saved twice
        $collection->save();

        $this->assertEquals(1, $l1->save_calls);
        $this->assertEquals(1, $l2->save_calls);

        $collection4 = new lmbAROneToManyCollection('lectures', $course);
        $arr = $collection4->getArray();
        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
    }

    function testLoadOnlyProperRecordsWithExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course1 = $this->_createCourseAndSave(array($l1, $l2));

        $l3 = $this->_createLecture();
        $l4 = $this->_createLecture();

        $course2 = $this->_createCourseAndSave(array($l3, $l4));

        $collection1 = new lmbAROneToManyCollection('lectures', $course1);
        $this->assertEquals(2, $collection1->count());
        $arr = $collection1->getArray();
        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());

        $collection2 = new lmbAROneToManyCollection('lectures', $course2);
        $this->assertEquals(2, $collection2->count());
        $arr = $collection2->getArray();
        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l3->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l4->getTitle());
    }

    function testCountWithExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = $this->_createCourseAndSave();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);

        $this->assertEquals(2, $collection->count());
    }

    function testCountWithNonSavedOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = $this->_initCourse();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $this->assertEquals(0, $collection->count());

        $collection->add($l1);
        $collection->add($l2);

        $this->assertEquals(2, $collection->count());
    }

    function testImplementsCountable()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = $this->_initCourse();
        $collection = new lmbAROneToManyCollection('lectures', $course);

        $this->assertEquals(0, sizeof($collection));

        $collection->add($l1);
        $collection->add($l2);

        $this->assertEquals(2, sizeof($collection));
    }

    function testPartiallyImplementsArrayAccess()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = $this->_initCourse();
        $collection = new lmbAROneToManyCollection('lectures', $course);

        $collection[] = $l1;
        $collection[] = $l2;

        $this->assertEquals($collection[0]->getId(), $l1->getId());
        $this->assertEquals($collection[1]->getId(), $l2->getId());
        $this->assertNull($collection[2]);

        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));
        $this->assertFalse(isset($collection[2]));

        //we can't really implement just every php array use case
        $this->assertNull($collection['foo']);
        $this->assertFalse(isset($collection['foo']));
        $collection[3] = 'foo';
        $this->assertNull($collection[3]);
    }

    function testRemoveAllWithExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = $this->_createCourseAndSave(array($l1, $l2));

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->removeAll();

        $course2 = lmbActiveRecord::findById(CourseForTestObject::class, $course->getId());

        $collection = new lmbAROneToManyCollection('lectures', $course2);
        $this->assertEquals(0, sizeof($collection->getArray()));
    }

    function testRemoveAllWithNonSavedOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = $this->_createCourse();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);
        $collection->removeAll();

        $this->assertEquals(0, $collection->count());
    }

    function testPaginateWithNonSavedOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $l3 = $this->_createLecture();

        $course = $this->_createCourse();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);
        $collection->add($l3);

        $collection->paginate($offset = 0, $limit = 2);

        $this->assertEquals(3, $collection->count());
        $arr = $collection->getArray();

        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
    }

    function testPaginateWithExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $l3 = $this->_createLecture();

        $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->paginate($offset = 0, $limit = 2);

        $this->assertEquals(3, $collection->count());
        $arr = $collection->getArray();

        $this->assertEquals(2, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
    }

    function testSortWithExistingOwner()
    {
        $l1 = $this->_createLecture('A-Lecture');
        $l2 = $this->_createLecture('B-Lecture');
        $l3 = $this->_createLecture('C-Lecture');

        $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->sort(array('title' => 'DESC'));

        $this->assertEquals(3, $collection->count());
        $arr = $collection->getArray();

        $this->assertEquals(3, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $l3->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $l2->getTitle());
        $this->assertEquals($arr[2]->getTitle(), $l1->getTitle());
    }

    function testSortWithNonSavedOwner()
    {
        $l1 = $this->_createLecture('A-Lecture');
        $l2 = $this->_createLecture('B-Lecture');
        $l3 = $this->_createLecture('C-Lecture');

        $course = $this->_createCourse();

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->add($l1);
        $collection->add($l2);
        $collection->add($l3);

        $collection->sort(array('title' => 'DESC'));
        $this->assertEquals('C-Lecture', $collection->at(0)->getTitle());
        $this->assertEquals('B-Lecture', $collection->at(1)->getTitle());
        $this->assertEquals('A-Lecture', $collection->at(2)->getTitle());
    }

    function testFindFirstWithSortParamsForExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $course = $this->_createCourseAndSave(array($l1, $l2));

        $lecture = $course->getLectures()->findFirst(array('sort' => array('id' => 'DESC')));
        $this->assertEquals($lecture->getTitle(), $l2->getTitle());
    }

    function testFindForExistingOwnerAppliesSortParamsFromRelationInfo()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = new SpecialCourseForTestObject();// lectures relation info has sort_params
        $course->setTitle('Special cource');
        $course->addToLectures($l1);
        $course->addToLectures($l2);
        $course->save();

        $lectures = $course->getLectures()->find();
        $this->assertEquals($lectures->at(0)->getTitle(), $l2->getTitle());
        $this->assertEquals($lectures->at(1)->getTitle(), $l1->getTitle());
    }

    function testFindForExistingOwnerAppliesSortParamsFromChildObjectByDefault()
    {
        $l1 = new SpecialLectureForTestObject();
        $l1->setTitle('lecture1');
        $l2 = new SpecialLectureForTestObject();
        $l2->setTitle('lecture2');

        $course = new VerySpecialCourseForTestObject();// lectures relation info has sort_params
        $course->setTitle('Special cource');
        $course->addToLectures($l1);
        $course->addToLectures($l2);
        $course->save();

        $lectures = $course->getLectures()->find();
        $this->assertEquals($lectures->at(0)->getTitle(), $l2->getTitle());
        $this->assertEquals($lectures->at(1)->getTitle(), $l1->getTitle());
    }

    function testIterateAlsoAppliesSortParamsFromRelationInfo()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();

        $course = new SpecialCourseForTestObject();// lectures relation info has sort_params
        $course->setTitle('Special cource');
        $course->addToLectures($l1);
        $course->addToLectures($l2);
        $course->save();

        $lectures = $course->getLectures();
        $this->assertEquals($lectures->at(0)->getTitle(), $l2->getTitle());
        $this->assertEquals($lectures->at(1)->getTitle(), $l1->getTitle());
    }

    function testAtWithExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $l3 = $this->_createLecture();

        $course = $this->_createCourseAndSave(array($l1, $l2, $l3));
        $collection = new lmbAROneToManyCollection('lectures', $course);

        $this->assertEquals($collection->at(0)->getTitle(), $l1->getTitle());
        $this->assertEquals($collection->at(2)->getTitle(), $l3->getTitle());
        $this->assertEquals($collection->at(1)->getTitle(), $l2->getTitle());
    }

    function testFindWithExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $l3 = $this->_createLecture();

        $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

        $lectures = $course->getLectures()->find(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId());
        $this->assertEquals(1, $lectures->count());
        $this->assertEquals($lectures->at(0)->getTitle(), $l1->getTitle());
    }

    function testFindWithNonSavedOwner_TODO()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $course = $this->_createCourse(array($l1, $l2));

        try {
            $lectures = $course->getLectures()->find(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId());
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testFindFirstWithExistingOwner()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $l3 = $this->_createLecture();

        $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

        $lecture = $course->getLectures()->findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId() . " OR " . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l2->getId());
        $this->assertEquals($lecture->getTitle(), $l1->getTitle());
    }

    function testFindFirstWithNonSavedOwner_TODO()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $course = $this->_createCourse(array($l1, $l2));

        try {
            $lecture = $course->getLectures()->findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId() . " OR " . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l2->getId());
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testApplyDecoratorWithParams()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $course = $this->_createCourseAndSave(array($l1, $l2));
        $lectures = $course->getLectures();
        $lectures->addDecorator(lmbARTestingDSDecorator::class, array('value' => 'my_value'));

        $this->assertEquals('my_value', $lectures->at(0)->get('value'));
        $this->assertEquals('my_value', $lectures->at(1)->get('value'));

        $lectures->rewind();
        $record = $lectures->current();
        $this->assertEquals('my_value', $record->get('value'));
    }

    function testSet()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $course = $this->_createCourseAndSave(array($l1, $l2));

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $collection->set(array($l2));

        $this->assertEquals(1, $collection->count());
        $this->assertEquals($collection->at(0)->getTitle(), $l2->getTitle());
    }

    function testSetExistringRelatedObjectIsSaved()
    {
        $l1 = $this->_createLecture();
        $l2 = $this->_createLecture();
        $course = $this->_createCourseAndSave(array($l1, $l2));

        $collection = new lmbAROneToManyCollection('lectures', $course);
        $l2->setTitle('new_title');
        $collection->set(array($l2));

        $this->assertEquals(1, $collection->count());
        $this->assertEquals('new_title', $collection->at(0)->getTitle());
    }

    function testGetRelatedObjectWithAdditionCriteria()
    {
        $l1 = $this->_createLecture('foo1');
        $l2 = $this->_createLecture('foo2');
        $l3 = $this->_createLecture('bar1');
        $l4 = $this->_createLecture('bar2');

        $course = $this->_createCourseAndSave(array($l1, $l2, $l3, $l4));

        $this->assertEquals(2, count($course->getFooLectures()));
    }

    protected function _initCourse($lectures = array())
    {
        $course = new CourseForTestObject();
        $course->setTitle('Course' . mt_rand());

        if (count($lectures)) {
            foreach ($lectures as $lecture)
                $course->addToLectures($lecture);
        }

        return $course;
    }

    protected function _createCourse($lectures = array())
    {
        $course = $this->_initCourse($lectures);
        return $course;
    }

    protected function _createCourseAndSave($lectures = array())
    {
        $course = $this->_createCourse($lectures);
        $course->save();
        return $course;
    }

    protected function _createLecture($title = '')
    {
        $title = $title ? $title : 'Lecture' . mt_rand();

        $l = new LectureForTestObject();
        $l->setTitle($title);
        return $l;
    }
}
