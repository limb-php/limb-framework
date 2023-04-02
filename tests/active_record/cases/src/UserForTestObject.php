<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class UserForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'user_for_test';

    protected $_has_many_to_many = array(
        'groups' => array(
            'field' => 'user_id',
            'foreign_field' => 'group_id',
            'table' => 'user_for_test2group_for_test',
            'class' => GroupForTestObject::class
        ),
        'cgroups' => array(
            'field' => 'user_id',
            'foreign_field' => 'group_id',
            'table' => 'user_for_test2group_for_test',
            'class' => GroupForTestObject::class,
            'criteria' =>'group_for_test.title="condition"'
        )
    );

    protected $_has_one = array('linked_object' => array(
        'field' => 'linked_object_id',
        'class' => TestOneTableObject::class,
        'can_be_null' => true));
}
