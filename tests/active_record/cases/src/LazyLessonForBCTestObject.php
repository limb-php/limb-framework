<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class LazyLessonForBCTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'lesson_for_test';
    protected $_lazy_attributes = array('date_start');
    protected $_composed_of = array('date_start' => array('field' => 'date_start',
        'class' => TestingValueObject::class,
        'getter' => 'getValue'),
        'date_end' => array('field' => 'date_end',
            'class' => TestingValueObject::class,
            'getter' => 'getValue'));

}