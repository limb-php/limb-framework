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
use limb\active_record\src\lmbARException;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\criteria\lmbSQLRawCriteria;
use Tests\active_record\cases\src\CourseForTestObject;
use Tests\active_record\cases\src\LectureForTestObject;
use Tests\active_record\cases\src\TestOneTableObject;
use Tests\active_record\cases\src\TestOneTableObject2;
use Tests\active_record\cases\src\TestOneTableObjectWithCustomDestroy;
use Tests\active_record\cases\src\TestOneTableObjectWithCustomProperty;
use Tests\active_record\cases\src\TestOneTableObjectWithHooks;
use Tests\active_record\cases\src\TestOneTableObjectWithSortParams;

//require_once (dirname(__FILE__) . '/.setup.php');

class lmbActiveRecordTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('test_one_table_object', 'lecture_for_test', 'course_for_test');

    function testGetDoesNotHaveProperty()
    {
        $object = new TestOneTableObject2();

        $this->assertEquals('foo_bar', $object->foo_bar); // should call $object->getFooBar()
    }

    function testArrayAccessConsidersDbFields()
    {
        $object = new TestOneTableObject();
        $this->assertTrue(isset($object['annotation']));
        unset($object['annotation']); // Does not make any sense since db fields always available
        $this->assertTrue(isset($object['annotation']));
    }

    function testGetCustomProperty()
    {
        $object = new TestOneTableObjectWithCustomProperty();
        $this->assertTrue($object->getCustomProperty());
    }

    function testGetWithDefaultValue()
    {
        $object = new TestOneTableObject();
        try {
            $object->get('foo');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertEquals('bar', $object->get('foo', 'bar'));
    }

    function testSaveNewRecord()
    {
        $object = new TestOneTableObject();
        $object->set('annotation', $annotation = 'Super annotation');
        $object->set('content', $content = 'Super content');
        $object->set('news_date', $news_date = '2005-01-10');

        $this->assertTrue($object->isNew());

        $id = $object->save();

        $this->assertFalse($object->isNew());
        $this->assertNotNull($object->getId());
        $this->assertEquals($object->getId(), $id);

        $this->assertEquals(1, $this->db->count('test_one_table_object'));

        $record = $this->db->selectRecord('test_one_table_object');
        $this->assertEquals($record->get('id'), $id);
        $this->assertEquals($record->get('annotation'), $annotation);
        $this->assertEquals($record->get('content'), $content);
        $this->assertEquals($record->get('news_date'), $news_date);
        $this->assertEquals($record->get('id'), $object->getId());
    }

    function testDontCreateNewRecordTwice()
    {
        $object = $this->creator->initOneTableObject();

        $object->save();
        $object->save();
        $object->save();
        $object->save();

        $this->assertEquals(1, $this->db->count('test_one_table_object'));

        $this->assertEquals(1, $object->getId());
    }

    function testIsNew()
    {
        $object = $this->creator->initOneTableObject();
        $this->assertTrue($object->isNew());

        $object->save();
        $this->assertFalse($object->isNew());

        $object->setIsNew();

        $this->assertTrue($object->isNew());
    }

    function testDetach()
    {
        $object = $this->creator->initOneTableObject();
        $this->assertTrue($object->isNew());

        $object->save();
        $this->assertFalse($object->isNew());
        $this->assertNotNull($object->getId());

        $object->detach();

        $this->assertTrue($object->isNew());
        $this->assertNull($object->getId());

        $object->save();

        $this->assertEquals(2, $this->db->count('test_one_table_object'));
    }

    function testSaveUpdate()
    {
        $object = $this->creator->createOneTableObject();

        $object->set('annotation', $annotation = 'Other annotation');
        $object->set('content', $content = 'Other content');
        $object->set('news_date', $news_date = '2005-10-20');
        $object->save();

        $this->assertEquals(1, $this->db->count('test_one_table_object'));

        $record = $this->db->selectRecord('test_one_table_object');

        $this->assertEquals($record->get('annotation'), $annotation);
        $this->assertEquals($record->get('content'), $content);
        $this->assertEquals($record->get('news_date'), $news_date);
        $this->assertEquals($record->get('id'), $object->getId());
    }

    function testProperOrderOfCreateHooksCalls()
    {
        $object = new TestOneTableObjectWithHooks();
        $object->setContent('whatever');

        ob_start();
        $object->save();
        $str = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('|on_before_save||on_before_create||on_validate||on_save||on_create||on_after_create||on_after_save|', $str);
    }

    function testProperOrderOfUpdateHooksCalls()
    {
        $object = new TestOneTableObjectWithHooks();
        $object->setContent('whatever');
        ob_start();
        $object->save();
        ob_end_clean();

        $object->setContent('other content');

        ob_start();
        $object->save();
        $str = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('|on_before_save||on_before_update||on_validate||on_save||on_update||on_after_update||on_after_save|', $str);
    }

    function testProperOrderOfDestroyHooksCalls()
    {
        $object = new TestOneTableObjectWithHooks();
        $object->setContent('whatever');
        ob_start();
        $object->save();
        ob_clean();

        $object->destroy();
        $str = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('|on_before_destroy||on_after_destroy|', $str);
    }

    function testFindById()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $found = lmbActiveRecord::findById(TestOneTableObject::class, $object2->getId());
        $this->assertEquals($found->export(), $object2->export());

        //testing convenient alias
        $found = TestOneTableObject::findById($object2->getId());
        $this->assertEquals($found->export(), $object2->export());
    }

    function testFindByIdThrowsExceptionIfNotFound()
    {
        try {
            lmbActiveRecord::findById(TestOneTableObject::class, -1000);
            $this->fail();
        } catch (lmbARException $e) {
            $this->assertTrue(true);
        }
    }

    function testFindByIdReturnsNullIfNotFound()
    {
        $this->assertNull(lmbActiveRecord::findById(TestOneTableObject::class, -1000, false));
    }

    function testLoadById()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $loaded = new TestOneTableObject();
        $loaded->loadById($object2->getId());
        $this->assertEquals($loaded->export(), $object2->export());
        $this->assertFalse($loaded->isNew());
    }

    function testLoadByIdThrowsExceptionIfNotFound()
    {
        $loaded = new TestOneTableObject();
        try {
            $loaded->loadById(-10000);
            $this->fail();
        } catch (lmbARException $e) {
            $this->assertTrue(true);
        }
    }

    function testPassingIntToConstructorLoadsObject()
    {
        $object1 = $this->creator->createOneTableObject();

        $object2 = new TestOneTableObject($object1->getId());
        $this->assertEquals($object2->export(), $object1->export());
        $this->assertFalse($object2->isNew());
    }

    function testPassingNonExistingIntToConstructorTrowsException()
    {
        try {
            $loaded = new TestOneTableObject(-10000);
            $this->fail();
        } catch (lmbARException $e) {
            $this->assertTrue(true);
        }
    }

    function testFindFirst()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();
        $this->assertFalse($object2->isNew());

        $found = lmbActiveRecord:: findFirst(TestOneTableObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());

        //testing convenient alias
        $found = TestOneTableObject:: findFirst(array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());
    }

    function testFindFirstConvertStringToCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();
        $this->assertFalse($object2->isNew());

        $found = lmbActiveRecord::findFirst(TestOneTableObject::class, lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());

        //testing convenient alias
        $found = TestOneTableObject::findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());
    }

    function testFindFirstConvertObjectToCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();
        $this->assertFalse($object2->isNew());

        $found = lmbActiveRecord::findFirst(TestOneTableObject::class, new lmbSQLRawCriteria(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());

        //testing convenient alias
        $found = TestOneTableObject::findFirst(new lmbSQLRawCriteria(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());
    }

    function testFindFirstConvertArrayToCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();
        $this->assertFalse($object2->isNew());

        $found = lmbActiveRecord::findFirst(TestOneTableObject::class, array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object1->getId()));
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());

        //testing convenient alias
        $found = TestOneTableObject::findFirst(array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object1->getId()));
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());
    }

    function testFindFirstWithSortParams()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $found = lmbActiveRecord::findFirst(TestOneTableObject::class, array('sort' => array('id' => 'DESC')));
        $this->assertEquals($found->get('id'), $object2->getId());

        //testing convenient alias
        $found = TestOneTableObject::findFirst(array('sort' => array('id' => 'DESC')));
        $this->assertEquals($found->get('id'), $object2->getId());
    }

    function testFindFirstWithDefaultSortParams()
    {
        $object1 = new TestOneTableObjectWithSortParams();
        $object1->setContent('Content' . mt_rand());
        $object1->save();

        $object2 = new TestOneTableObjectWithSortParams();
        $object2->setContent('Content' . mt_rand());
        $object2->save();

        $found = lmbActiveRecord::findFirst(TestOneTableObjectWithSortParams::class);
        $this->assertEquals($found->get('id'), $object2->getId());

        //testing convenient alias
        $found = TestOneTableObjectWithSortParams::findFirst();
        $this->assertEquals($found->get('id'), $object2->getId());
    }

    function testFindOneAlias()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();
        $this->assertFalse($object2->isNew());

        $found = lmbActiveRecord::findFirst(TestOneTableObject::class, lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());

        //testing convenient alias
        $found = TestOneTableObject::findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
        $this->assertEquals($found->get('annotation'), $object1->get('annotation'));
        $this->assertEquals($found->get('content'), $object1->get('content'));
        $this->assertEquals($found->get('news_date'), $object1->get('news_date'));
        $this->assertEquals($found->get('id'), $object1->getId());
    }

    function testFindAllRecordsNoCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $object = new TestOneTableObject();
        $rs = $object->findAllRecords();
        $rs->rewind();
        $this->assertEquals($object1->getId(), $rs->current()->get('id'));
        $rs->next();
        $this->assertEquals($object2->getId(), $rs->current()->get('id'));
    }

    function testFildAllRecordsWithCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $object = new TestOneTableObject();
        $rs = $object->findAllRecords(new lmbSQLFieldCriteria('id', $object2->getId()));
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->get('id'));
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testFindAllNoCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::find(TestOneTableObject::class);
        $rs->rewind();
        $this->assertEquals($object1->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertEquals($object2->getId(), $rs->current()->getId());

        //testing convenient alias
        $rs = TestOneTableObject::find();
        $rs->rewind();
        $this->assertEquals($object1->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
    }

    function testFindAllWithCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::find(TestOneTableObject::class, array('criteria' => new lmbSQLFieldCriteria('id', $object2->getId())));
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());

        //testing convenient alias
        $rs = TestOneTableObject::find(array('criteria' => new lmbSQLFieldCriteria('id', $object2->getId())));
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testFindConvertObjectToCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::find(TestOneTableObject::class, new lmbSQLFieldCriteria('id', $object2->getId()));
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());

        //testing convenient alias
        $rs = TestOneTableObject::find(new lmbSQLFieldCriteria('id', $object2->getId()));
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testFindConvertStringToCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::find(TestOneTableObject::class, lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());

        //testing convenient alias
        $rs = TestOneTableObject::find(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testFindConvertArrayToCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::find(TestOneTableObject::class, array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object2->getId()));
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());

        //testing convenient alias
        $rs = TestOneTableObject::find(array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object2->getId()));
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testFindWithIntegerCallsFindById()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $object = lmbActiveRecord::find(TestOneTableObject::class, $object2->getId());
        $this->assertEquals($object2->getId(), $object->getId());

        //testing convenient alias
        $object = TestOneTableObject::find($object2->getId());
        $this->assertEquals($object2->getId(), $object->getId());
    }

    function testFindWithIntegerDoesNotThrowException()
    {
        $this->assertNull(lmbActiveRecord::find(TestOneTableObject::class, -10000));

        //testing convenient alias
        $this->assertNull(TestOneTableObject::find(-10000));
    }

    function testFindAllWithSortParams()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::find(TestOneTableObject::class, array('sort' => array('id' => 'DESC')));
        $arr = $rs->getArray();
        $this->assertEquals($arr[0]->get('id'), $object2->getId());
        $this->assertEquals($arr[1]->get('id'), $object1->getId());

        //testing convenient alias
        $rs = TestOneTableObject::find(array('sort' => array('id' => 'DESC')));
        $arr = $rs->getArray();
        $this->assertEquals($arr[0]->get('id'), $object2->getId());
        $this->assertEquals($arr[1]->get('id'), $object1->getId());
    }

    function testFindAllWithDefaultSortParams()
    {
        $object1 = new TestOneTableObjectWithSortParams();
        $object1->setContent('Content' . mt_rand());
        $object1->save();

        $object2 = new TestOneTableObjectWithSortParams();
        $object2->setContent('Content' . mt_rand());
        $object2->save();

        $rs = lmbActiveRecord::find(TestOneTableObjectWithSortParams::class, array('sort' => array('id' => 'DESC')));
        $arr = $rs->getArray();
        $this->assertEquals($arr[0]->get('id'), $object2->getId());
        $this->assertEquals($arr[1]->get('id'), $object1->getId());

        //testing convenient alias
        $rs = TestOneTableObject::find(array('sort' => array('id' => 'DESC')));
        $arr = $rs->getArray();
        $this->assertEquals($arr[0]->get('id'), $object2->getId());
        $this->assertEquals($arr[1]->get('id'), $object1->getId());
    }

    function testFindWithRelatedObjects_UsingWithParam()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();
        $alt_course1 = $this->creator->createCourse();
        $alt_course2 = $this->creator->createCourse();
        $lecture1 = $this->creator->createLecture($course1, $alt_course1);
        $lecture2 = $this->creator->createLecture($course2, $alt_course2);
        $lecture3 = $this->creator->createLecture($course1, $alt_course2);

        $rs = lmbActiveRecord::find(LectureForTestObject::class, array('join' => 'course, alt_course'));
        $arr = $rs->getArray();

        //make sure we really eager fetching
        $this->db->delete('course_for_test');

        $this->assertEquals($arr[0]->getId(), $lecture1->getId());
        $this->assertEquals($arr[0]->getCourse()->getTitle(), $course1->getTitle());
        $this->assertEquals($arr[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

        $this->assertEquals($arr[1]->getId(), $lecture2->getId());
        $this->assertEquals($arr[1]->getCourse()->getTitle(), $course2->getTitle());
        $this->assertEquals($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());

        $this->assertEquals($arr[2]->getId(), $lecture3->getId());
        $this->assertEquals($arr[2]->getCourse()->getTitle(), $course1->getTitle());
        $this->assertEquals($arr[2]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    }

    function testFindAttachRelatedObjects_HasMany()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course1, null, 'ZZZ');
        $lecture2 = $this->creator->createLecture($course2, null, 'CCC');
        $lecture3 = $this->creator->createLecture($course1, null, 'AAA');
        $lecture4 = $this->creator->createLecture($course1, null, 'BBB');

        $rs = lmbActiveRecord::find(CourseForTestObject::class, array('attach' => array('lectures' => array('sort' => array('title' => 'ASC')))));
        $arr = $rs->getArray();

        //make sure we really eager fetching
        $this->db->delete('lecture_for_test');

        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $course1->getTitle());
        $lectures = $arr[0]->getLectures();
        $this->assertCount(3, $lectures);
        $this->assertEquals($lectures[0]->getId(), $lecture3->getId());
        $this->assertEquals('AAA', $lectures[0]->getTitle());
        $this->assertEquals($lectures[1]->getId(), $lecture4->getId());
        $this->assertEquals('BBB', $lectures[1]->getTitle());
        $this->assertEquals($lectures[2]->getId(), $lecture1->getId());
        $this->assertEquals('ZZZ', $lectures[2]->getTitle());

        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getTitle(), $course2->getTitle());
        $lectures = $arr[1]->getLectures();
        $this->assertCount(1, $lectures);
        $this->assertEquals($lectures[0]->getId(), $lecture2->getId());
        $this->assertEquals('CCC', $lectures[0]->getTitle());
    }

    function testFindBySql()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::findBySql(TestOneTableObject::class, 'select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertEquals($object1->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());

        $this->assertEquals($rs->getIds(), array($object2->getId(), $object1->getId()));

        //testing convenient alias
        $rs = TestOneTableObject::findBySql('select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
        $rs->rewind();
        $this->assertEquals($object2->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertEquals($object1->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testFindFirstBySql()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $object = lmbActiveRecord::findFirstBySql(TestOneTableObject::class, 'select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
        $this->assertEquals($object2->getId(), $object->getId());

        //testing convenient alias
        $object = TestOneTableObject:: findFirstBySql('select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
        $this->assertEquals($object2->getId(), $object->getId());
    }

    function testFindOneBySqlAlias()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $object = lmbActiveRecord::findFirstBySql(TestOneTableObject::class, 'select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
        $this->assertEquals($object2->getId(), $object->getId());

        //testing convenient alias
        $object = TestOneTableObject::findFirstBySql('select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
        $this->assertEquals($object2->getId(), $object->getId());
    }

    function testFindByIds()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();
        $object3 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::findByIds(TestOneTableObject::class,
            array($object1->getId(), $object3->getId()),
            array('sort' => array('id' => 'asc')));
        $rs->rewind();
        $this->assertEquals($object1->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertEquals($object3->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());

        //testing convenient alias
        $rs = TestOneTableObject::findByIds(array($object1->getId(), $object3->getId()), array('sort' => array('id' => 'asc')));
        $rs->rewind();
        $this->assertEquals($object1->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertEquals($object3->getId(), $rs->current()->getId());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

//  function testFindByIdsWithCriteria()
//  {
//    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
//    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
//    $object3 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
//    $object4 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
//
//    $rs = lmbActiveRecord :: findByIds(TestOneTableObject::class,
//                                       array($object1->getId(), $object3->getId(), $object4->getId()),
//                                       array('sort' => array('id' => 'asc'),
//                                             'criteria' => 'id <> 3'));
//    $rs->rewind();
//    $this->assertEquals($object1->getId(), $rs->current()->getId());
//    $rs->next();
//    $this->assertEquals($object4->getId(), $rs->current()->getId());
//    $rs->next();
//    $this->assertFalse($rs->valid());
//
//    //testing convenient alias
//    $rs = TestOneTableObject :: findByIds(array($object1->getId(), $object3->getId(), $object4->getId()), array('sort' => array('id' => 'asc'), 'criteria' => 'id <> 3'));
//    $rs->rewind();
//    $this->assertEquals($object1->getId(), $rs->current()->getId());
//    $rs->next();
//    $this->assertEquals($object4->getId(), $rs->current()->getId());
//    $rs->next();
//    $this->assertFalse($rs->valid());
//  }

    function testFindByIdsReturnEmptyIteratorIfNoIds()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $rs = lmbActiveRecord::findByIds(TestOneTableObject::class, array());
        $rs->rewind();
        $this->assertFalse($rs->valid());

        //testing convenient alias
        $rs = TestOneTableObject::findByIds(array());
        $rs->rewind();
        $this->assertFalse($rs->valid());
    }

//  function testGetDatasetActsAsStaticFind()
//  {
//    $object1 = $this->creator->createOneTableObject();
//    $object2 = $this->creator->createOneTableObject();
//
//    $ds = $object2->getDataset();
//    $this->assertEquals($ds->at(0)->getId(), $object1->getId());
//    $this->assertEquals($ds->at(1)->getId(), $object2->getId());
//  }

    function testDelete()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        lmbActiveRecord::delete(TestOneTableObject::class);
        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteShort()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        TestOneTableObject::delete();
        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteCallsDestroy()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        ob_start();
        lmbActiveRecord::delete(TestOneTableObjectWithCustomDestroy::class);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('destroyed!destroyed!', $contents);
        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteShortCallsDestroy()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        ob_start();
        TestOneTableObjectWithCustomDestroy:: delete();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('destroyed!destroyed!', $contents);
        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteByCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
        lmbActiveRecord:: delete(TestOneTableObject::class, $criteria);

        $this->assertEquals(1, $this->db->count('test_one_table_object'));

        $found = lmbActiveRecord:: findById(TestOneTableObject::class, $object1->getId());
        $this->assertEquals($found->getContent(), $object1->getContent());
    }

    function testDeleteShortByCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
        TestOneTableObject::delete($criteria);

        $this->assertEquals(1, $this->db->count('test_one_table_object'));

        $found = TestOneTableObject:: findById($object1->getId());
        $this->assertEquals($found->getContent(), $object1->getContent());
    }

    function testDeleteRaw()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        lmbActiveRecord::deleteRaw(TestOneTableObject::class);

        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteShortRaw()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        TestOneTableObject::deleteRaw();

        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteRawDoesntCallDestroy()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        ob_start();
        lmbActiveRecord::deleteRaw(TestOneTableObjectWithCustomDestroy::class);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('', $contents);
        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteShortRawDoesntCallDestroy()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        ob_start();
        TestOneTableObjectWithCustomDestroy::deleteRaw();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('', $contents);
        $this->assertEquals(0, $this->db->count('test_one_table_object'));
    }

    function testDeleteRawByCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
        lmbActiveRecord::deleteRaw(TestOneTableObject::class, $criteria);

        $this->assertEquals(1, $this->db->count('test_one_table_object'));

        $found = lmbActiveRecord::findById(TestOneTableObject::class, $object1->getId());
        $this->assertEquals($found->getContent(), $object1->getContent());
    }

    function testDeleteShortRawByCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
        TestOneTableObject::deleteRaw($criteria);

        $this->assertEquals(1, $this->db->count('test_one_table_object'));

        $found = TestOneTableObject::findById($object1->getId());
        $this->assertEquals($found->getContent(), $object1->getContent());
    }

    function testUpdateRawAllWithArraySet()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        lmbActiveRecord::updateRaw(TestOneTableObject::class, array('content' => 'blah'));

        $rs = lmbActiveRecord::find(TestOneTableObject::class);
        $rs->rewind();
        $this->assertEquals('blah', $rs->current()->getContent());
        $rs->next();
        $this->assertEquals('blah', $rs->current()->getContent());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testUpdateShortRawAllWithArraySet()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        TestOneTableObject::updateRaw(array('content' => 'blah'));

        $rs = TestOneTableObject::find();
        $rs->rewind();
        $this->assertEquals('blah', $rs->current()->getContent());
        $rs->next();
        $this->assertEquals('blah', $rs->current()->getContent());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testUpdateRawAllWithRawValues()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        lmbActiveRecord::updateRaw(TestOneTableObject::class, lmbActiveRecord::getDefaultConnection()->quoteIdentifier("ordr") . '=1');

        $rs = lmbActiveRecord::find(TestOneTableObject::class);
        $rs->rewind();
        $this->assertEquals(1, $rs->current()->getOrdr());
        $rs->next();
        $this->assertEquals(1, $rs->current()->getOrdr());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testUpdateShortRawAllWithRawValues()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        TestOneTableObject::updateRaw(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("ordr") . '=1');

        $rs = TestOneTableObject::find();
        $rs->rewind();
        $this->assertEquals(1, $rs->current()->getOrdr());
        $rs->next();
        $this->assertEquals(1, $rs->current()->getOrdr());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testUpdateRawWithCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        lmbActiveRecord::updateRaw(TestOneTableObject::class, array('content' => 'blah'), lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());

        $rs = lmbActiveRecord::find(TestOneTableObject::class);
        $rs->rewind();
        $this->assertEquals($rs->current()->getContent(), $object1->getContent());
        $rs->next();
        $this->assertEquals('blah', $rs->current()->getContent());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testUpdateShortRawWithCriteria()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        TestOneTableObject::updateRaw(array('content' => 'blah'), lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());

        $rs = TestOneTableObject::find();
        $rs->rewind();
        $this->assertEquals($rs->current()->getContent(), $object1->getContent());
        $rs->next();
        $this->assertEquals('blah', $rs->current()->getContent());
        $rs->next();
        $this->assertFalse($rs->valid());
    }

    function testGetTableName()
    {
        $object = new TestOneTableObject();
        $this->assertEquals('test_one_table_object', $object->getTableName());
    }
}
