<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class ProgramForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'program_for_test';

    protected $_has_many = array('courses' => array(
        'field' => 'program_id',
        'class' => CourseForTestObject::class
    ),

        'cached_lectures' => array(
            'field' => 'program_id',
            'class' => LectureForTestObject::class
        ));
}
