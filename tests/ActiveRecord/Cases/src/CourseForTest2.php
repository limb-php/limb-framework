<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class CourseForTest2 extends cachedActiveRecord
{
    protected $_db_table_name = 'course_for_test';
    protected $_has_many = array(
        'lectures' => array('field' => 'course_id',
        'class' => LectureForTest2::class)
    );
}
