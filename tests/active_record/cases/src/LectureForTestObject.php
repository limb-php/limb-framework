<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class LectureForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'lecture_for_test';
    protected $_many_belongs_to = array('course' => array('field' => 'course_id',
        'class' => CourseForTestObject::class
    ),
        'alt_course' => array('field' => 'alt_course_id',
            'class' => CourseForTestObject::class,
            'can_be_null' => true
        ),
        'cached_program' => array('field' => 'program_id',
            'class' => ProgramForTestObject::class
        ));

    protected $_test_validator;

    function setValidator($validator)
    {
        $this->_test_validator = $validator;
    }

    function _createValidator()
    {
        if($this->_test_validator)
            return $this->_test_validator;

        return parent::_createValidator();
    }
}
