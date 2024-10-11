<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class VerySpecialCourseForTestObject extends CourseForTestObject
{
    protected $_has_many = array('lectures' => array(
        'field' => 'course_id',
        'class' => SpecialLectureForTestObject::class
    ));
}
