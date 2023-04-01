<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\FooLectureForTest;
use tests\active_record\cases\TypedLectureForTest;

class CourseForTestForTypedLecture extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_typed_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => TypedLectureForTest::class),
        'foo_lectures' => array('field' => 'course_id',
            'class' => FooLectureForTest::class));
}