<?php

namespace Tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class CourseWithNullableLectures extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => LectureIndependentFromCourse::class,
        'nullify' => true),
    );
}
