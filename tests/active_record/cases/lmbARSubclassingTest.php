<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\active_record\cases;

//require_once (dirname(__FILE__) . '/.setup.php');

use limb\active_record\src\lmbActiveRecord;
use limb\active_record\src\lmbARException;
use limb\active_record\src\lmbARNotFoundException;
use limb\dbal\src\criteria\lmbSQLCriteria;
use Tests\active_record\cases\src\BarFooLectureForTestObject;
use Tests\active_record\cases\src\BarFooOneTableTestObject;
use Tests\active_record\cases\src\CourseForTestForTypedLecture;
use Tests\active_record\cases\src\FooLectureForTestObject;
use Tests\active_record\cases\src\FooOneTableTestObject;
use Tests\active_record\cases\src\TestOneTableTypedObject;

class lmbARSubclassingTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('lecture_for_typed_test', 'course_for_typed_test', 'test_one_table_typed_object');

    function testCreate()
    {
        $object1 = new FooOneTableTestObject();
        $object1->setTitle('Some title');
        $object1->save();

        $object2 = new FooOneTableTestObject($object1->getId());
        $this->assertEquals($object2->getTitle(), $object1->getTitle());

        //parents are supertypes..
        $object3 = new TestOneTableTypedObject($object1->getId());
        $this->assertEquals($object3->getTitle(), $object1->getTitle());

        try {
            //..while deeper subclasses are not
            new BarFooOneTableTestObject($object1->getId());
            $this->fail();
        } catch (lmbARException $e) {

        }
    }

    function testSupertypeDelete()
    {
        $foo = new FooOneTableTestObject();
        $foo->setTitle('Some title');
        $foo->save();

        $bar = new BarFooOneTableTestObject();
        $bar->setTitle('Another title');
        $bar->save();

        lmbActiveRecord:: delete(TestOneTableTypedObject::class);

        $rs = lmbActiveRecord:: find(FooOneTableTestObject::class);
        $this->assertEquals(0, $rs->count());

        $rs = lmbActiveRecord:: find(BarFooOneTableTestObject::class);
        $this->assertEquals(0, $rs->count());
    }

    function testTypedDelete()
    {
        $foo = new FooOneTableTestObject();
        $foo->setTitle('Some title');
        $foo->save();

        $bar = new BarFooOneTableTestObject();
        $bar->setTitle('Another title');
        $bar->save();

        lmbActiveRecord:: delete(BarFooOneTableTestObject::class);//removing subclass

        $rs = lmbActiveRecord:: find(BarFooOneTableTestObject::class);
        $this->assertEquals(0, $rs->count());

        $rs = lmbActiveRecord:: find(FooOneTableTestObject::class);//supertype stays
        $this->assertEquals(1, $rs->count());

        lmbActiveRecord:: delete(FooOneTableTestObject::class);//removing supertype

        $rs = lmbActiveRecord:: find(FooOneTableTestObject::class);
        $this->assertEquals(0, $rs->count());
    }

    function testFind()
    {
        $object1 = new FooOneTableTestObject();
        $object1->setTitle('Some title');
        $object1->save();

        $object2 = new BarFooOneTableTestObject();
        $object2->setTitle('Some other title');
        $object2->save();

        $rs = lmbActiveRecord::find(FooOneTableTestObject::class);//supertype
        $this->assertEquals(2, $rs->count());
        $this->assertInstanceOf(FooOneTableTestObject::class, $rs->at(0));
        $this->assertInstanceOf(BarFooOneTableTestObject::class, $rs->at(1));

        $rs = lmbActiveRecord::find(BarFooOneTableTestObject::class);//subclass
        $this->assertEquals(1, $rs->count());
        $this->assertInstanceOf(BarFooOneTableTestObject::class, $rs->at(0));
    }

    function testFindWithKind()
    {
        $valid_object1 = new BarFooOneTableTestObject();
        $valid_object1->setTitle('title1');
        $valid_object1->save();

        $valid_object2 = new BarFooOneTableTestObject();
        $valid_object2->setTitle('title2');
        $valid_object2->save();

        $wrong_class_object = new FooOneTableTestObject();
        $wrong_class_object->setTitle('title1');
        $wrong_class_object->save();

        $wrong_title_object = new FooOneTableTestObject();
        $wrong_title_object->setTitle('wrong_title');
        $wrong_title_object->save();

        $criteria = new lmbSQLCriteria();
        $criteria->add(lmbSQLCriteria::equal('title', 'title1'));
        $criteria->addOr(lmbSQLCriteria::equal('title', 'title2'));

        $records = lmbActiveRecord:: find(BarFooOneTableTestObject::class, $criteria)->sort(array('id'))->getArray();
        $this->assertCount(2, $records);
        $this->assertEquals($records[0]->title, $valid_object1->title);
        $this->assertEquals($records[1]->title, $valid_object2->title);
    }

    function testTypedRelationFind()
    {
        $course = new CourseForTestForTypedLecture();
        $course->setTitle('Source1');
        $course->save();

        $lecture1 = new FooLectureForTestObject();
        $lecture1->setTitle('Some title');
        $lecture1->setCourse($course);
        $lecture1->save();

        $lecture2 = new BarFooLectureForTestObject();
        $lecture2->setTitle('Some other title');
        $lecture2->setCourse($course);
        $lecture2->save();

        $course->getLectures()->add($lecture1);
        $course->getLectures()->add($lecture2);

        $course2 = new CourseForTestForTypedLecture($course->getId());

        $this->assertEquals(2, $course2->getLectures()->count());//supertype by default
        $this->assertInstanceOf(FooLectureForTestObject::class, $course2->getLectures()->at(0));
        $this->assertInstanceOf(BarFooLectureForTestObject::class, $course2->getLectures()->at(1));

        //narrowing selection but again its supertype for BarFooLectureForTest
        $lectures = $course2->getLectures()->find(array('class' => FooLectureForTestObject::class));

        $this->assertEquals(2, $lectures->count());
        $this->assertInstanceOf(FooLectureForTestObject::class, $lectures->at(0));
        $this->assertInstanceOf(BarFooLectureForTestObject::class, $lectures->at(1));

        //narrowing more
        $lectures = $course2->getLectures()->find(array('class' => BarFooLectureForTestObject::class));
        $this->assertEquals(1, $lectures->count());
        $this->assertInstanceOf(BarFooLectureForTestObject::class, $lectures->at(0));
    }
}
