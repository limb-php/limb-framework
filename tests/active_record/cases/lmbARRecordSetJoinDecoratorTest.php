<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\active_record\cases;

use limb\active_record\src\lmbARRecordSetJoinDecorator;
use limb\dbal\src\lmbDBAL;
use limb\dbal\src\lmbSimpleDb;
use limb\toolkit\src\lmbToolkit;
use Tests\active_record\cases\src\CourseForTestObject;
use Tests\active_record\cases\src\LectureForTestObject;

class lmbARRecordSetJoinDecoratorTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test');

    function testProcessPrefixedFieldsAsRelatedActiveRecords()
    {
        $course = $this->_createCourseWithTwoLectures();
        $lecture = new LectureForTestObject();
        $course_info = $lecture->getRelationInfo('course');

        $conn = lmbToolkit:: instance()->getDefaultDbConnection();
        $db = new lmbSimpleDb($conn);
        $sql = 'SELECT ' . $conn->quoteIdentifier("lecture_for_test") . '.*, ' . $conn->quoteIdentifier("course_for_test.id") . ' as ' . $conn->quoteIdentifier("course__id") . ', ' . $conn->quoteIdentifier("course_for_test.title") . ' as ' . $conn->quoteIdentifier("course__title") . ' 
            FROM ' . $conn->quoteIdentifier("lecture_for_test") . ' LEFT JOIN ' . $conn->quoteIdentifier("course_for_test") . ' ON ' . $conn->quoteIdentifier("course_for_test.id") . ' = ' . $conn->quoteIdentifier("lecture_for_test.course_id");
        $decorated = lmbDBAL:: fetch($sql);

        $iterator = new lmbARRecordSetJoinDecorator($decorated, new LectureForTestObject(), null, array('course' => $course_info));

        // let's fetch all data in order to actually call rewind() and current();
        $arr = $iterator->getArray();

        // now let's remove everything from db tables so we can be sure that processing is correct
        $db->delete('lecture_for_test');
        $db->delete('course_for_test');

        $this->assertEquals($arr[0]->get('course')->getTitle(), $course->getTitle());
        $this->assertEquals($arr[1]->get('course')->getTitle(), $course->getTitle());
    }

    function _createCourseWithTwoLectures()
    {
        $course = new CourseForTestObject();
        $course->setTitle($title = 'General Course');

        $l1 = new LectureForTestObject();
        $l1->setTitle('Physics');
        $l2 = new LectureForTestObject();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);
        $course->save();

        return $course;
    }
}
