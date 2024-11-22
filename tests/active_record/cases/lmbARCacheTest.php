<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\active_record\cases;

use limb\cache\src\lmbCacheFileWithMetaBackend;
use limb\cache\src\lmbCacheGroupDecorator;
use limb\core\src\lmbEnv;
use tests\active_record\cases\src\CourseForTest2;
use tests\active_record\cases\src\LectureForTest2;

class lmbARCacheTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('course_for_test', 'lecture_for_test');
    protected $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
        $this->cache = new lmbCacheGroupDecorator(new lmbCacheFileWithMetaBackend($cache_dir));
        //$this->cache->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        //$this->cache->flush();
    }

    function testCacheHasCourse()
    {
        $lecture = $this->_initLecture();
        $this->assertTrue(isset($lecture['course']));

        $lecture->save();

        $this->cache->set($key = $lecture->getId(), $lecture, null, array('group' => $group = $lecture->getClass()));

        if (null !== ($lecture2 = $this->cache->get($key, null, array('group' => $group)))) {
            $this->assertTrue(isset($lecture2['course']));
        } else {
            $this->fail();
        }
    }

    function testCacheSaveCollection()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTest2();
        $l1->setTitle('Physics');
        $l2 = new LectureForTest2();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        $this->cache->set($key_l1 = $l1->getId(), $l1, null, array('group' => $group_l1 = $l1->getClass()));
        $this->cache->set($key_l2 = $l2->getId(), $l2, null, array('group' => $group_l2 = $l2->getClass()));
        $this->cache->set($key = $course->getId(), $course, null, array('group' => $group = $course->getClass()));

        if (null !== ($course2 = $this->cache->get($key, null, array('group' => $group)))) {
            $rs = $course2->getLectures();

            $rs->rewind();
            $this->assertEquals($rs->current()->getTitle(), $l1->getTitle());
            $rs->next();
            $this->assertEquals($rs->current()->getTitle(), $l2->getTitle());
        } else {
            $this->fail();
        }

        if (null !== ($l12 = $this->cache->get($key_l1, null, array('group' => $group_l1)))) {
            $this->assertEquals($l12->getTitle(), $l1->getTitle());

            $this->assertEquals($l12->getCourse()->getTitle(), $l1->getCourse()->getTitle());
        } else {
            $this->fail();
        }
    }

    function testCacheSaveCollectionCrash()
    {
        $course = $this->_initCourse();

        $l1 = new LectureForTest2();
        $l1->setTitle('Physics');
        $l2 = new LectureForTest2();
        $l2->setTitle('Math');

        $course->addToLectures($l1);
        $course->addToLectures($l2);

        $course->save();

        /* */
        $l12 = LectureForTest2::findById($l1->getId());
        $l122 = LectureForTest2::findById($l1->getId());

        $this->cache->set($key_l12 = $l12->getId(), $l12, null, array('group' => $group_l12 = $l12->getClass()));

        if (null !== ($l12c = $this->cache->get($key_l12, null, array('group' => $group_l12)))) {
            $this->assertEquals($l12->getTitle(), $l12c->getTitle());
            $this->assertEquals($l12->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
            $this->assertEquals($l122->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
        } else {
            $this->fail();
        }

        /* */
        $l12 = LectureForTest2::findById($l1->getId(), array('attach' => array('course')));
        $l122 = LectureForTest2::findById($l1->getId(), array('attach' => array('course')));
        $this->cache->set($key_l12 = $l12->getId(), $l12, null, array('group' => $group_l12 = $l12->getClass()));

        if (null !== ($l12c = $this->cache->get($key_l12, null, array('group' => $group_l12)))) {
            $this->assertEquals($l12->getTitle(), $l12c->getTitle());

            $this->assertEquals($l12->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
            $this->assertEquals($l122->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
        } else {
            $this->fail();
        }
    }

    function _initCourse()
    {
        $course = new CourseForTest2();
        $course->setTitle('Course' . mt_rand());
        return $course;
    }

    function _initLecture()
    {
        $lecture = new LectureForTest2();
        $lecture->setTitle('Lecture' . mt_rand());
        return $lecture;
    }
}
