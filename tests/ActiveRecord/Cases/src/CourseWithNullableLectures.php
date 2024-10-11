<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class CourseWithNullableLectures extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => LectureIndependentFromCourse::class,
        'nullify' => true),
    );
}
