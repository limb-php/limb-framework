<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class CourseForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => LectureForTestObject::class),
        'alt_lectures' => array('field' => 'alt_course_id',
            'class' => LectureForTestObject::class),
        'foo_lectures' => array('field' => 'course_id',
            'class' => LectureForTestObject::class,
            'criteria' => 'lecture_for_test.title like "foo%"'));

    protected $_many_belongs_to = array('program' => array('field' => 'program_id',
        'class' => ProgramForTestObject::class,
        'can_be_null' => true));

    public $save_calls = 0;

    function save($error_list = null)
    {
        parent::save();
        $this->save_calls++;
    }
}
