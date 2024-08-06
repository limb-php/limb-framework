<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\src\NameForAggregateTest;

class MemberForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'member_for_test';

    protected $_composed_of = array(
        'name' => array(
        'class' => NameForAggregateTest::class,
        'mapping' => array(
            'first' => 'first_name',
            'last' => 'last_name'),
        'setup_method' => 'setupName'));

    public $saved_full_name = '';

    function setupName($name_object)
    {
        $this->saved_full_name = $name_object->getFull();
        return $name_object;
    }
}
