<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class CourseForTestForTypedLecture extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_typed_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => TypedLectureForTestObject::class),
        'foo_lectures' => array('field' => 'course_id',
            'class' => FooLectureForTestObject::class));
}
