<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class LectureIndependentFromCourse extends lmbActiveRecord
{
    protected $_db_table_name = 'lecture_for_test';
    protected $_many_belongs_to = array('course' => array(
        'field' => 'course_id',
        'class' => CourseWithNullableLectures::class,
        'can_be_null' => true,
        'throw_exception_on_not_found' => false),
    );
}
