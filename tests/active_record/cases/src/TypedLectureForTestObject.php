<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\src\CourseForTestForTypedLecture;

class TypedLectureForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'lecture_for_typed_test';
    protected $_belongs_to = array('course' => array(
        'field' => 'course_id',
        'class' => CourseForTestForTypedLecture::class));
}
