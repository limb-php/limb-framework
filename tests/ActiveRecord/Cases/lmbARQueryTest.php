<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\ActiveRecord\Cases;

use limb\active_record\lmbARException;
use limb\active_record\lmbARQuery;
use limb\dbal\criteria\lmbSQLCriteria;
use Limb\Tests\ActiveRecord\Cases\src\CourseForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\LectureForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\PersonForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\ProgramForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\SocialSecurityForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\TestOneTableObject;
use Limb\Tests\ActiveRecord\Cases\src\UserForTestObject;

class lmbARQueryTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('test_one_table_object',
        'program_for_test',
        'person_for_test',
        'social_security_for_test',
        'lecture_for_test',
        'course_for_test');

    function testSimpleFetch()
    {
        $object1 = $this->creator->createOneTableObject();
        $object2 = $this->creator->createOneTableObject();

        $query = lmbARQuery::create(TestOneTableObject::class, array(), $this->conn);
        $iterator = $query->fetch();
        $arr = $iterator->getArray();

        $this->assertInstanceOf(TestOneTableObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getAnnotation(), $object1->getAnnotation());
        $this->assertInstanceOf(TestOneTableObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getAnnotation(), $object2->getAnnotation());
    }

    function testSimpleFetch_WithSort()
    {
        $object1 = $this->creator->createOneTableObject(10);
        $object2 = $this->creator->createOneTableObject(20);

        $query = lmbARQuery::create(TestOneTableObject::class, array('sort' => array('ordr' => 'DESC')), $this->conn);
        $iterator = $query->fetch();
        $iterator->sort(array('id' => 'ASC'));
        $arr = $iterator->getArray();

        $this->assertInstanceOf(TestOneTableObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getAnnotation(), $object1->getAnnotation());
        $this->assertInstanceOf(TestOneTableObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getAnnotation(), $object2->getAnnotation());
    }

    function testGetRecordSetWIthSort()
    {
        $object1 = $this->creator->createOneTableObject(10);
        $object2 = $this->creator->createOneTableObject(20);

        $query = lmbARQuery:: create(TestOneTableObject::class, array('sort' => array('ordr' => 'DESC')), $this->conn);
        $iterator = $query->getRecordSet();
        $arr = $iterator->getArray();

        $this->assertEquals($arr[0]->get('annotation'), $object2->getAnnotation());
        $this->assertEquals($arr[1]->get('annotation'), $object1->getAnnotation());
    }

    function testFetch_Join_RelatedHasOneObject()
    {
        $person1 = $this->creator->createPerson();
        $person2 = $this->creator->createPerson();

        $this->conn->resetStats();

        $query = lmbARQuery:: create(PersonForTestObject::class, array(), $this->conn);
        $query->eagerJoin('social_security');
        $iterator = $query->fetch();
        $arr = $iterator->getArray();

        $this->assertEquals($this->conn->countQueries(), 1);

        //make sure we really eager fetching
        $this->db->delete('social_security_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(PersonForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getName(), $person1->getName());
        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[0]->getSocialSecurity());
        $this->assertEquals($arr[0]->getSocialSecurity()->getCode(), $person1->getSocialSecurity()->getCode());

        $this->assertInstanceOf(PersonForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getName(), $person2->getName());
        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[1]->getSocialSecurity());
        $this->assertEquals($arr[1]->getSocialSecurity()->getCode(), $person2->getSocialSecurity()->getCode());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_Join_RelatedBelongsToObject()
    {
        $person1 = $this->creator->createPerson();
        $ss1 = $person1->getSocialSecurity();
        $person2 = $this->creator->createPerson();
        $ss2 = $person2->getSocialSecurity();

        $this->conn->resetStats();

        $query = lmbARQuery:: create(SocialSecurityForTestObject::class, array(), $this->conn);
        $query->eagerJoin('person');
        $iterator = $query->fetch();
        $arr = $iterator->getArray();

        $this->assertEquals($this->conn->countQueries(), 1);

        //make sure we really eager fetching
        $this->db->delete('person_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getCode(), $ss1->getCode());
        $this->assertInstanceOf(PersonForTestObject::class, $arr[0]->getPerson());
        $this->assertEquals($arr[0]->getPerson()->getName(), $person1->getName());

        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getCode(), $ss2->getCode());
        $this->assertInstanceOf(PersonForTestObject::class, $arr[1]->getPerson());
        $this->assertEquals($arr[1]->getPerson()->getName(), $person2->getName());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_Join_RelatedManyBelongsToObject()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();
        $lecture1 = $this->creator->createLecture($course1);
        $lecture2 = $this->creator->createLecture($course1);
        $lecture3 = $this->creator->createLecture($course2);

        $this->conn->resetStats();

        $query = lmbARQuery:: create(LectureForTestObject::class, array(), $this->conn);
        $query->eagerJoin('course');
        $iterator = $query->fetch();
        $arr = $iterator->getArray();

        $this->assertEquals($this->conn->countQueries(), 1);

        //make sure we really eager fetching
        $this->db->delete('course_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(LectureForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $lecture1->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]->getCourse());
        $this->assertEquals($arr[0]->getCourse()->getTitle(), $course1->getTitle());

        $this->assertInstanceOf(LectureForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getTitle(), $lecture2->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]->getCourse());
        $this->assertEquals($arr[1]->getCourse()->getTitle(), $course1->getTitle());

        $this->assertInstanceOf(LectureForTestObject::class, $arr[2]);
        $this->assertEquals($arr[2]->getTitle(), $lecture3->getTitle());
        $this->assertInstanceOf(CourseForTestObject::class, $arr[2]->getCourse());
        $this->assertEquals($arr[2]->getCourse()->getTitle(), $course2->getTitle());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_Attach_RelatedHasOneObjects()
    {
        $person1 = $this->creator->createPerson();
        $person2 = $this->creator->createPerson();

        $this->conn->resetStats();

        $query = lmbARQuery:: create(PersonForTestObject::class, array(), $this->conn);
        // note attach() has the same effect as join() but works in a different way - it produces another sql request
        $iterator = $query->eagerAttach('social_security')->fetch();
        $arr = $iterator->getArray();

        $this->assertEquals($this->conn->countQueries(), 2);

        //make sure we really eager fetching
        $this->db->delete('social_security_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(PersonForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getName(), $person1->getName());
        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[0]->getSocialSecurity());
        $this->assertEquals($arr[0]->getSocialSecurity()->getCode(), $person1->getSocialSecurity()->getCode());

        $this->assertInstanceOf(PersonForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getName(), $person2->getName());
        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[1]->getSocialSecurity());
        $this->assertEquals($arr[1]->getSocialSecurity()->getCode(), $person2->getSocialSecurity()->getCode());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_Attach_WhenEmptyRecordSet_ForHasOneRelation()
    {
        $this->conn->resetStats();

        $query = lmbARQuery::create(PersonForTestObject::class, array(), $this->conn);
        // note attach() has the same effect as join() but workds is a different way - it produces another sql request
        $iterator = $query->eagerAttach('social_security')->fetch();
        $arr = $iterator->getArray();
        $this->assertEquals(sizeof($arr), 0);

        $this->assertEquals($this->conn->countQueries(), 1);
    }

    function testFetch_Attach_WhenEmptyRecordSet_ForBelongsToRelation()
    {
        $this->conn->resetStats();

        $query = lmbARQuery::create(SocialSecurityForTestObject::class, array(), $this->conn);
        // note attach() has the same effect as join() but workds is a different way - it produces another sql request
        $iterator = $query->eagerAttach('person')->fetch();
        $arr = $iterator->getArray();
        $this->assertEquals(sizeof($arr), 0);

        $this->assertEquals($this->conn->countQueries(), 1);
    }

    function testFetch_Attach_RelatedBelongsToObjects()
    {
        $id = $this->db->insert('person_for_test', array('id' => 100, 'name' => 'junky person'));

        $person1 = $this->creator->createPerson();
        $person2 = $this->creator->createPerson();

        $this->db->delete('person_for_test', $this->conn->quoteIdentifier("id") . '= ' . $id);

        $this->conn->resetStats();

        $query = lmbARQuery:: create(SocialSecurityForTestObject::class, array(), $this->conn);
        // note attach() has the same effect as join() but workds is a different way - it produces another sql request
        $arr = $query->eagerAttach('person')->fetch()->getArray();

        $this->assertEquals($this->conn->countQueries(), 2);

        //make sure we really eager fetching
        $this->db->delete('person_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getCode(), $person1->getSocialSecurity()->getCode());
        $this->assertInstanceOf(PersonForTestObject::class, $arr[0]->getPerson());
        $this->assertEquals($arr[0]->getPerson()->getName(), $person1->getName());

        $this->assertInstanceOf(SocialSecurityForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getCode(), $person2->getSocialSecurity()->getCode());
        $this->assertInstanceOf(PersonForTestObject::class, $arr[1]->getPerson());
        $this->assertEquals($arr[1]->getPerson()->getName(), $person2->getName());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_Attach_RelatedManyBelongsToObjects()
    {
        $course = $this->creator->createCourse();

        $alt_course1 = $this->creator->createCourse();
        $alt_course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course, $alt_course1);
        $lecture2 = $this->creator->createLecture($course, $alt_course2);
        $lecture3 = $this->creator->createLecture($course, $alt_course1);

        $this->conn->resetStats();

        $query = lmbARQuery::create(LectureForTestObject::class, array(), $this->conn);
        $arr = $query->eagerAttach('course')->eagerAttach('alt_course')->fetch()->getArray();

        $this->assertEquals($this->conn->countQueries(), 3);

        //make sure we really eager fetching
        $this->db->delete('course_for_test');

        $this->conn->resetStats();

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

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_Attach_RelatedHasMany()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course1, null, 'ZZZ');
        $lecture2 = $this->creator->createLecture($course2, null, 'CCC');
        $lecture3 = $this->creator->createLecture($course1, null, 'AAA');
        $lecture4 = $this->creator->createLecture($course1, null, 'BBB');

        $this->conn->resetStats();

        $query = lmbARQuery::create(CourseForTestObject::class, array(), $this->conn);
        $arr = $query->eagerAttach('lectures', array('sort' => array('title' => 'ASC')))->fetch()->getArray();

        $this->assertEquals($this->conn->countQueries(), 2);

        //make sure we really eager fetching
        $this->db->delete('lecture_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $course1->getTitle());
        $lectures = $arr[0]->getLectures();
        $this->assertEquals(count($lectures), 3);
        $this->assertEquals($lectures[0]->getId(), $lecture3->getId());
        $this->assertEquals($lectures[0]->getTitle(), 'AAA');
        $this->assertEquals($lectures[1]->getId(), $lecture4->getId());
        $this->assertEquals($lectures[1]->getTitle(), 'BBB');
        $this->assertEquals($lectures[2]->getId(), $lecture1->getId());
        $this->assertEquals($lectures[2]->getTitle(), 'ZZZ');

        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getTitle(), $course2->getTitle());
        $lectures = $arr[1]->getLectures();
        $this->assertEquals(count($lectures), 1);
        $this->assertEquals($lectures[0]->getId(), $lecture2->getId());
        $this->assertEquals($lectures[0]->getTitle(), 'CCC');

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_Attach_RelatedHasMany_WithCriteriaForAttach()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course1, null, 'ZZZ');
        $lecture2 = $this->creator->createLecture($course2, null, 'CCC');
        $lecture3 = $this->creator->createLecture($course1, null, 'AAA');

        $query = lmbARQuery::create(CourseForTestObject::class, array(), $this->conn);
        $arr = $query->eagerAttach('lectures', array('criteria' => lmbSQLCriteria:: equal('title', 'CCC')))->fetch()->getArray();

        $this->conn->resetStats();

        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $course1->getTitle());
        $lectures = $arr[0]->getLectures();
        $this->assertEquals(count($lectures), 0);

        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getTitle(), $course2->getTitle());
        $lectures = $arr[1]->getLectures();
        $this->assertCount(1, $lectures);
        $this->assertEquals($lectures[0]->getId(), $lecture2->getId());
        $this->assertEquals('CCC', $lectures[0]->getTitle());

        $this->assertEquals(0, $this->conn->countQueries());

        // let's change the first course and save it. The lectures should stay in database
        $arr[0]->setTitle('Changed');
        $arr[0]->save();

        $loaded_course = new CourseForTestObject($course1->getId());
        $lectures = $loaded_course->getLectures();
        $this->assertEquals(2, count($lectures));
    }

    function testFetch_Attach_WithEmptyRS_ForRelatedHasMany()
    {
        $this->conn->resetStats();

        $query = lmbARQuery::create(CourseForTestObject::class, array(), $this->conn);
        $arr = $query->eagerAttach('lectures')->fetch()->getArray();

        $this->assertEquals(1, $this->conn->countQueries());
    }

    function testFetch_Attach_RelatedHasManyToMany()
    {
        $user1 = $this->creator->createUser();
        $user2 = $this->creator->createUser();

        $group1 = $this->creator->createGroup('AAA');
        $group2 = $this->creator->createGroup('BBB');
        $group3 = $this->creator->createGroup('ZZZ');

        $group1->setUsers(array($user1, $user2));
        $group2->setUsers(array($user2));
        $group3->setUsers(array($user1));

        $this->conn->resetStats();

        $query = lmbARQuery::create(UserForTestObject::class, array(), $this->conn);
        $arr = $query->eagerAttach('groups', array('sort' => array('title' => 'DESC')))->fetch()->getArray();

        $this->assertEquals($this->conn->countQueries(), 2);

        //make sure we really eager fetching
        $this->db->delete('group_for_test');
        $this->db->delete('user_for_test2group_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(UserForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getFirstName(), $user1->getFirstName());
        $groups = $arr[0]->getGroups();
        $this->assertEquals(count($groups), 2);
        $this->assertEquals($groups[0]->getId(), $group3->getId());
        $this->assertEquals($groups[0]->getTitle(), 'ZZZ');
        $this->assertEquals($groups[1]->getId(), $group1->getId());
        $this->assertEquals($groups[1]->getTitle(), 'AAA');

        $this->assertInstanceOf(UserForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getFirstName(), $user2->getFirstName());
        $groups = $arr[1]->getGroups();
        $this->assertEquals(count($groups), 2);
        $this->assertEquals($groups[0]->getId(), $group2->getId());
        $this->assertEquals($groups[0]->getTitle(), 'BBB');
        $this->assertEquals($groups[1]->getId(), $group1->getId());
        $this->assertEquals($groups[1]->getTitle(), 'AAA');

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_NestedJoinProperty_In_Attach_ForHasMany()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $alt_course1 = $this->creator->createCourse();
        $alt_course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course1, $alt_course2);
        $lecture2 = $this->creator->createLecture($course2, $alt_course1);
        $lecture3 = $this->creator->createLecture($course1, $alt_course2);
        $lecture4 = $this->creator->createLecture($course1, $alt_course1);

        $this->conn->resetStats();

        $query = lmbARQuery::create(CourseForTestObject::class, array(), $this->conn);
        $query->where(lmbSQLCriteria::in('id', [$course1->getId(), $course2->getId()]));
        $rs = $query->eagerAttach('lectures', array('join' => 'alt_course'))->fetch();
        $arr = $rs->getArray();

        $this->assertEquals(2, $this->conn->countQueries());

        //make sure we really eager fetching
        $this->db->delete('lecture_for_test');
        $this->db->delete('course_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $course1->getTitle());

        $lectures = $arr[0]->getLectures()->getArray();
        $this->assertCount(3, $lectures);
        $this->assertEquals($lecture1->getId(), $lectures[0]->getId());
        $this->assertEquals($lecture3->getId(), $lectures[1]->getId());
        $this->assertEquals($lecture4->getId(), $lectures[2]->getId());
        $this->assertEquals($alt_course2->getId(), $lectures[0]->getAltCourse()->getId());
        $this->assertEquals($alt_course2->getId(), $lectures[1]->getAltCourse()->getId());
        $this->assertEquals($alt_course1->getId(), $lectures[2]->getAltCourse()->getId());

        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getTitle(), $course2->getTitle());
        $lectures = $arr[1]->getLectures()->getArray();
        $this->assertCount(1, $lectures);
        $this->assertEquals($lectures[0]->getId(), $lecture2->getId());
        $this->assertEquals($lectures[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

        $this->assertEquals(0, $this->conn->countQueries());
    }

    function testFetch_NestedAttachProperty_In_Join()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $alt_course1 = $this->creator->createCourse();
        $alt_course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course1, $alt_course2);
        $lecture2 = $this->creator->createLecture($course2, $alt_course1);
        $lecture3 = $this->creator->createLecture($course1, $alt_course2);
        $lecture4 = $this->creator->createLecture($course1, $alt_course1);

        $lecture5 = $this->creator->createLecture($alt_course2);
        $lecture6 = $this->creator->createLecture($alt_course1);
        $lecture7 = $this->creator->createLecture($alt_course2);
        $lecture8 = $this->creator->createLecture($alt_course1);

        $this->conn->resetStats();

        $query = lmbARQuery:: create(LectureForTestObject::class, array(), $this->conn);
        $query->where(lmbSQLCriteria:: equal('course_id', $course1->getId()));
        $iterator = $query->eagerJoin('alt_course', array('attach' => 'lectures'))->fetch();
        $arr = $iterator->getArray();

        $this->assertEquals($this->conn->countQueries(), 2);

        //make sure we really eager fetching
        $this->db->delete('lecture_for_test');
        $this->db->delete('course_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(LectureForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $lecture1->getTitle());

        $this->assertEquals($arr[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
        $alt_course_lectures = $arr[0]->getAltCourse()->getLectures();
        $this->assertEquals($alt_course_lectures[0]->getId(), $lecture5->getId());
        $this->assertEquals($alt_course_lectures[1]->getId(), $lecture7->getId());

        $this->assertEquals($arr[1]->getId(), $lecture3->getId());
        $this->assertEquals($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
        $alt_course_lectures = $arr[1]->getAltCourse()->getLectures();
        $this->assertEquals($alt_course_lectures[0]->getId(), $lecture5->getId());
        $this->assertEquals($alt_course_lectures[1]->getId(), $lecture7->getId());

        $this->assertEquals($arr[2]->getId(), $lecture4->getId());
        $this->assertEquals($arr[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
        $alt_course_lectures = $arr[2]->getAltCourse()->getLectures();
        $this->assertEquals($alt_course_lectures[0]->getId(), $lecture6->getId());
        $this->assertEquals($alt_course_lectures[1]->getId(), $lecture8->getId());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetchNested_AttachProperty_In_JoinProperty_In_Attach()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $alt_course1 = $this->creator->createCourse();
        $alt_course2 = $this->creator->createCourse();

        $lecture1 = $this->creator->createLecture($course1, $alt_course2);
        $lecture2 = $this->creator->createLecture($course2, $alt_course1);
        $lecture3 = $this->creator->createLecture($course1, $alt_course2);
        $lecture4 = $this->creator->createLecture($course1, $alt_course1);

        $lecture5 = $this->creator->createLecture($alt_course2);
        $lecture6 = $this->creator->createLecture($alt_course1);
        $lecture7 = $this->creator->createLecture($alt_course2);
        $lecture8 = $this->creator->createLecture($alt_course1);

        $this->conn->resetStats();

        $query = lmbARQuery:: create(CourseForTestObject::class, array(), $this->conn);
        $query->where(lmbSQLCriteria:: in('id', array($course1->getId(), $course2->getId())));
        $arr = $query->eagerAttach('lectures', array('join' => array('alt_course' => array('attach' => 'lectures'))))->fetch()->getArray();

        $this->assertEquals($this->conn->countQueries(), 3);

        //make sure we really eager fetching
        $this->db->delete('lecture_for_test');
        $this->db->delete('course_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(CourseForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $course1->getTitle());
        $lectures = $arr[0]->getLectures()->getArray();
        $this->assertEquals(count($lectures), 3);
        $this->assertEquals($lectures[0]->getId(), $lecture1->getId());
        $this->assertEquals($lectures[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
        $alt_course_lectures = $lectures[0]->getAltCourse()->getLectures();
        $this->assertEquals($alt_course_lectures[0]->getId(), $lecture5->getId());
        $this->assertEquals($alt_course_lectures[1]->getId(), $lecture7->getId());

        $this->assertEquals($lectures[1]->getId(), $lecture3->getId());
        $this->assertEquals($lectures[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
        $alt_course_lectures = $lectures[1]->getAltCourse()->getLectures();
        $this->assertEquals($alt_course_lectures[0]->getId(), $lecture5->getId());
        $this->assertEquals($alt_course_lectures[1]->getId(), $lecture7->getId());

        $this->assertEquals($lectures[2]->getId(), $lecture4->getId());
        $this->assertEquals($lectures[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
        $alt_course_lectures = $lectures[2]->getAltCourse()->getLectures();
        $this->assertEquals($alt_course_lectures[0]->getId(), $lecture6->getId());
        $this->assertEquals($alt_course_lectures[1]->getId(), $lecture8->getId());

        $this->assertInstanceOf(CourseForTestObject::class, $arr[1]);
        $this->assertEquals($arr[1]->getTitle(), $course2->getTitle());
        $lectures = $arr[1]->getLectures()->getArray();
        $this->assertEquals(count($lectures), 1);
        $this->assertEquals($lectures[0]->getId(), $lecture2->getId());
        $this->assertEquals($lectures[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

        $alt_course_lectures = $lectures[0]->getAltCourse()->getLectures();
        $this->assertEquals($alt_course_lectures[0]->getId(), $lecture6->getId());
        $this->assertEquals($alt_course_lectures[1]->getId(), $lecture8->getId());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_NestedJoinProperty_In_Join()
    {
        $program1 = $this->creator->createProgram();
        $program2 = $this->creator->createProgram();
        $course1 = $this->creator->createCourse($program1);
        $course2 = $this->creator->createCourse($program2);
        $lecture1 = $this->creator->createLecture($course1);
        $lecture2 = $this->creator->createLecture($course2);

        $this->conn->resetStats();

        $query = lmbARQuery::create(LectureForTestObject::class, array(), $this->conn);
        $iterator = $query->eagerJoin('course', array('join' => 'program'))->fetch();
        $arr = $iterator->getArray();

        $this->assertEquals($this->conn->countQueries(), 1);

        //make sure we really eager fetching
        $this->db->delete('lecture_for_test');
        $this->db->delete('course_for_test');
        $this->db->delete('program_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(LectureForTestObject::class, $arr[0]);
        $this->assertEquals($arr[0]->getTitle(), $lecture1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $lecture2->getTitle());

        $this->assertEquals($arr[0]->getCourse()->getTitle(), $course1->getTitle());
        $this->assertEquals($arr[1]->getCourse()->getTitle(), $course2->getTitle());

        $this->assertEquals($arr[0]->getCourse()->getProgram()->getTitle(), $program1->getTitle());
        $this->assertEquals($arr[1]->getCourse()->getProgram()->getTitle(), $program2->getTitle());

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_NestedAttachProperty_In_Attach()
    {
        $program1 = $this->creator->createProgram();
        $program2 = $this->creator->createProgram();
        $course1 = $this->creator->createCourse($program1);
        $course2 = $this->creator->createCourse($program2);
        $course3 = $this->creator->createCourse($program1);
        $course4 = $this->creator->createCourse($program2);
        $lecture1 = $this->creator->createLecture($course1);
        $lecture2 = $this->creator->createLecture($course2);
        $lecture3 = $this->creator->createLecture($course3);
        $lecture4 = $this->creator->createLecture($course4);
        $lecture5 = $this->creator->createLecture($course1);
        $lecture6 = $this->creator->createLecture($course2);
        $lecture7 = $this->creator->createLecture($course3);
        $lecture8 = $this->creator->createLecture($course4);

        /*
         * program1 -> $course1, $course3
         * program2 -> $course2, $course4
         *
         * $course1 -> $lecture1, $lecture5
         * $course2 -> $lecture2, $lecture6
         * $course3 -> $lecture3, $lecture7
         * $course4 -> $lecture4, $lecture8
         */

        $this->conn->resetStats();

        $query = lmbARQuery::create(ProgramForTestObject::class, array(), $this->conn);
        $iterator = $query->eagerAttach('courses', array('attach' => 'lectures'))->fetch();

        $arr = $iterator->getArray();

        $this->assertEquals($this->conn->countQueries(), 3);

        //make sure we really eager fetching
        $this->db->delete('lecture_for_test');
        $this->db->delete('course_for_test');
        $this->db->delete('program_for_test');

        $this->conn->resetStats();

        $this->assertInstanceOf(ProgramForTestObject::class, $arr[0]);
        $this->assertEquals($program1->getTitle(), $arr[0]->getTitle());

        $courses = $arr[0]->getCourses()->getArray(); // courses for program1: 0 -> $course1; 1 -> $course3

        $this->assertEquals($course1->getTitle(), $courses[0]->getTitle());

        $lectures = $courses[0]->getLectures()->getArray();
        $this->assertEquals($lecture1->getTitle(), $lectures[0]->getTitle());
        $this->assertEquals($lecture5->getTitle(), $lectures[1]->getTitle());

        $this->assertEquals($courses[1]->getTitle(), $course3->getTitle());

        $lectures = $courses[1]->getLectures()->getArray();
        $this->assertEquals($lecture3->getTitle(), $lectures[0]->getTitle());
        $this->assertEquals($lecture7->getTitle(), $lectures[1]->getTitle());

        $this->assertEquals($program2->getTitle(), $arr[1]->getTitle());

        $courses = $arr[1]->getCourses()->getArray();  // courses for program2: 0 -> $course2; 1 -> $course4
        $this->assertEquals($course2->getTitle(), $courses[0]->getTitle());

        $lectures = $courses[0]->getLectures()->getArray();
        $this->assertEquals($lecture2->getTitle(), $lectures[0]->getTitle());
        $this->assertEquals($lecture6->getTitle(), $lectures[1]->getTitle());

        $this->assertEquals($course4->getTitle(), $courses[1]->getTitle());

        $lectures = $courses[1]->getLectures()->getArray();
        $this->assertEquals($lecture4->getTitle(), $lectures[0]->getTitle());
        $this->assertEquals($lecture8->getTitle(), $lectures[1]->getTitle());

        $this->assertEquals(0, $this->conn->countQueries());
    }

    function testFetch_JoinWorkdsOkIfJoinedObjectIsNotSet()
    {
        $program = $this->creator->createProgram();
        $course1 = $this->creator->createCourse($program);
        $course2 = $this->creator->createCourse();

        $query = lmbARQuery:: create(CourseForTestObject::class, array(), $this->conn);
        $arr = $query->eagerJoin('program')->fetch()->getArray();

        $this->assertEquals($arr[0]->getProgram()->getTitle(), $program->getTitle());
        $this->assertNull($arr[1]->getProgram());
    }

    function testFetch_AttachWithNothingToAttach()
    {
        $program1 = $this->creator->createProgram();
        $program2 = $this->creator->createProgram();

        $this->conn->resetStats();

        $query = lmbARQuery::create(ProgramForTestObject::class, array(), $this->conn);
        $arr = $query->eagerAttach('courses')->fetch()->getArray();

        $this->assertEquals($this->conn->countQueries(), 2);

        $this->conn->resetStats();

        $this->assertEquals($arr[0]->getTitle(), $program1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $program2->getTitle());
        $this->assertEquals($arr[0]->getCourses()->count(), 0);
        $this->assertEquals($arr[1]->getCourses()->count(), 0);

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testFetch_JoinWithWrongRelationType()
    {
        $program1 = $this->creator->createProgram();
        $program2 = $this->creator->createProgram();

        $query = lmbARQuery:: create(ProgramForTestObject::class, array(), $this->conn);
        $query->eagerJoin('courses');
        try {
            $it = $query->fetch();
            $this->assertTrue(false);
        } catch (lmbARException $e) {
            $this->assertTrue(true);
        }
    }

    function testFetch_AttachManyBelongsToRelationWithNothingToAttach()
    {
        $course1 = $this->creator->createCourse();
        $course2 = $this->creator->createCourse();

        $this->conn->resetStats();

        $query = lmbARQuery:: create(CourseForTestObject::class, array(), $this->conn);
        $arr = $query->eagerAttach('program')->fetch()->getArray();

        $this->assertEquals($this->conn->countQueries(), 2);

        $this->conn->resetStats();
        $this->assertEquals($arr[0]->getTitle(), $course1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $course2->getTitle());

        $this->assertNull($arr[0]->getProgram(), 0);
        $this->assertNull($arr[1]->getProgram(), 0);

        $this->assertEquals($this->conn->countQueries(), 0);
    }

    function testGroup()
    {
        $course1 = $this->creator->createCourse();
        $lecture1 = $this->creator->createLecture($course1);
        $lecture2 = $this->creator->createLecture($course1);

        $course2 = $this->creator->createCourse();
        $lecture3 = $this->creator->createLecture($course2);
        $lecture4 = $this->creator->createLecture($course2);

        $query = lmbARQuery:: create(LectureForTestObject::class, array('group' => 'course.id'), $this->conn);
        $rs = $query->eagerJoin('course')->fetch();

        $this->assertEquals($rs->count(), 2);

        $arr = $rs->getArray();
        $this->assertEquals(count($arr), 2);
        $this->assertEquals($arr[0]->getTitle(), $lecture1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $lecture3->getTitle());
    }
}
