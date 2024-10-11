<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class LectureForTest2 extends cachedActiveRecord
{
    protected $_db_table_name = 'lecture_for_test';

    protected $_many_belongs_to = array(
        'course' => array(
            'field' => 'course_id',
            'class' => CourseForTest2::class
        ),
    );
}
