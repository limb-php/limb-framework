<?php

namespace tests\active_record\cases\src;

class LessonWithNullObjectForBCTestObject extends LessonForBCTestObject
{
    protected $_db_table_name = 'lesson_for_test';

    function getNotRequiredDate()
    {
        $null_object = new TestingValueObject('null');
        return $this->get('not_required_date', $null_object);
    }
}