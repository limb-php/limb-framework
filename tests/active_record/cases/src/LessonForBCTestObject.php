<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class LessonForBCTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'lesson_for_test';

    protected $_composed_of = array(
        'date_start' => array(
            'field' => 'date_start',
            'class' => TestingValueObject::class,
            'getter' => 'getValue'),
        'date_end' => array(
            'field' => 'date_end',
            'class' => TestingValueObject::class,
            'getter' => 'getValue'
        ),
        'not_required_date' => array(
            'field' => 'date_end',
            'class' => TestingValueObject::class,
            'getter' => 'getValue',
            'can_be_null' => true
        ));
}