<?php

namespace tests\active_record\cases\src;

class lmbARTestingObjectMother
{
    function initOneTableObject($ordr = '')
    {
        $object = new TestOneTableObject();
        $object->set('annotation', 'Annotation ' . rand(0, 1000));
        $object->set('content', 'Content ' . rand(0, 1000));
        $object->set('news_date', date("Y-m-d", time()));
        $ordr = $ordr ? $ordr : rand(0, 1000);
        $object->set('ordr', $ordr);
        return $object;
    }

    function createOneTableObject($ordr = '')
    {
        $object = $this->initOneTableObject($ordr);
        $object->save();
        return $object;
    }

    function initPerson()
    {
        $person = new PersonForTestObject();
        $person->setName('Person_' . rand(0, 1000));
        return $person;
    }

    function createPerson()
    {
        $person = $this->initPerson();

        $number = $this->createSocialSecurity($person);
        $person->setSocialSecurity($number);
        $person->save();
        return $person;
    }

    function initSocialSecurity()
    {
        $number = new SocialSecurityForTestObject();
        $number->setCode(rand(0, 1000));
        return $number;
    }

    function createSocialSecurity($person)
    {
        $number = $this->initSocialSecurity();
        $number->setPerson($person);
        return $number;
    }

    function createCourse($program = null)
    {
        $course = new CourseForTestObject();
        $course->setTitle('Course_' . rand(1, 999));

        if ($program)
            $course->setProgram($program);

        $course->save();
        return $course;
    }

    function createProgram()
    {
        $program = new ProgramForTestObject();
        $program->setTitle('Program_' . rand(1, 999));
        $program->save();
        return $program;
    }

    function createLecture($course, $alt_course = null, $title = '')
    {
        $lecture = new LectureForTestObject();
        $title = $title ? $title : 'Lecture_' . rand(1, 999);
        $lecture->setTitle($title);
        $lecture->setCourse($course);

        if ($alt_course)
            $lecture->setAltCourse($alt_course);

        $lecture->save();
        return $lecture;
    }

    function initUser($linked_object = null)
    {
        $user = new UserForTestObject();
        $user->setFirstName('User_' . rand(1, 999));

        if ($linked_object)
            $user->setLinkedObject($linked_object);

        return $user;
    }

    function createUser($linked_object = null)
    {
        $user = $this->initUser($linked_object);
        $user->save();
        return $user;
    }

    function initGroup($title = '')
    {
        $group = new GroupForTestObject();
        $title = $title ? $title : 'Group_' . rand(1, 999);
        $group->setTitle($title);
        return $group;
    }

    function createGroup($title = '')
    {
        $group = $this->initGroup($title);
        $group->save();
        return $group;
    }
}
