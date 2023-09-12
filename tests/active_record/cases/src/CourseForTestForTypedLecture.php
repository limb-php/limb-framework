<?php

namespace Tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class CourseForTestForTypedLecture extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_typed_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => TypedLectureForTestObject::class),
        'foo_lectures' => array('field' => 'course_id',
            'class' => FooLectureForTestObject::class));
}
