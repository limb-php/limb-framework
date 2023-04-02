<?php

namespace tests\active_record\cases\src;

class SpecialCourseForTestObject extends CourseForTestObject
{
    protected $_has_many = array('lectures' => array(
        'field' => 'course_id',
        'class' => LectureForTestObject::class,
        'sort_params' => array('id' => 'DESC')));
}
