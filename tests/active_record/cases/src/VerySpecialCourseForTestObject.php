<?php
namespace tests\active_record\cases\src;

class VerySpecialCourseForTestObject extends CourseForTestObject
{
    protected $_has_many = array('lectures' => array(
        'field' => 'course_id',
        'class' => SpecialLectureForTestObject::class
    ));
}
