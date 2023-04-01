<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class UserForTestWithSpecialRelationTable extends lmbActiveRecord
{
    protected $_db_table_name = 'user_for_test';

    protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
        'foreign_field' => 'group_id',
        'table' => 'extended_user_for_test2group_for_test',
        'class' => GroupForTestObject::class));
}