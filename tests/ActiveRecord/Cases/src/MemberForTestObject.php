<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class MemberForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'member_for_test';

    protected $_composed_of = array(
        'name' => array(
            'class' => NameForAggregateTest::class,
            'mapping' => array(
                'first' => 'first_name',
                'last' => 'last_name'
            ),
            'setup_method' => 'setupName'
        ),
        'new_name' => array(
            'class' => NameForAggregateTest::class,
            'mapping' => array(
                'first' => 'first_name',
                'last' => 'last_name'
            ),
            'setup_method' => 'setupNameNoExists'
        )
    );

    public $saved_full_name = '';

    function setupName($name_object)
    {
        $this->saved_full_name = $name_object->getFull();
        return $name_object;
    }
}
