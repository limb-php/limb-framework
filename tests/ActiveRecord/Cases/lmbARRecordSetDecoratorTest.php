<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\ActiveRecord\Cases;

use limb\active_record\lmbARRecordSetDecorator;
use limb\core\lmbCollection;
use limb\dbal\lmbSimpleDb;
use limb\toolkit\lmbToolkit;
use Limb\Tests\ActiveRecord\Cases\src\CourseForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\LectureForTestObject;

class lmbARRecordSetDecoratorTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test');

    function testCreateActiveRecordFromCurrentRecord()
    {
        $course = $this->_createCourseWithTwoLectures();

        $db = new lmbSimpleDb(lmbToolkit:: instance()->getDefaultDbConnection());
        $decorated = $db->select('lecture_for_test');

        $iterator = new lmbARRecordSetDecorator($decorated, LectureForTestObject::class);
        $iterator->rewind();

        $lecture1 = $iterator->current();
        $this->assertEquals($lecture1->getCourse()->getTitle(), $course->getTitle());

        $iterator->next();
        $lecture2 = $iterator->current();
        $this->assertEquals($lecture2->getCourse()->getTitle(), $course->getTitle());
    }

    function testGetOffsetIsDecorated()
    {
        $course = $this->_createCourseWithTwoLectures();

        $db = new lmbSimpleDb(lmbToolkit:: instance()->getDefaultDbConnection());
        $decorated = $db->select('lecture_for_test');

        $iterator = new lmbARRecordSetDecorator($decorated, LectureForTestObject::class);

        $this->assertEquals($iterator->at(0)->getCourse()->getTitle(), $course->getTitle());
        $this->assertEquals($iterator[0]->getCourse()->getTitle(), $course->getTitle());

        $this->assertEquals($iterator->at(1)->getCourse()->getTitle(), $course->getTitle());
        $this->assertEquals($iterator[1]->getCourse()->getTitle(), $course->getTitle());
    }

    function testIfRecordIsEmpty()
    {
        $iterator = new lmbARRecordSetDecorator(new lmbCollection(), LectureForTestObject::class);
        $iterator->rewind();
        $this->assertFalse($iterator->valid());
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
