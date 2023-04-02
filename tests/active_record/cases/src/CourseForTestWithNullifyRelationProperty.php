<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class CourseForTestWithNullifyRelationProperty extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => LectureForTestObject::class,
        'nullify' => true));
}
