<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class GroupForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'group_for_test';

    protected $_has_many_to_many = array('users' => array('field' => 'group_id',
        'foreign_field' => 'user_id',
        'table' => 'user_for_test2group_for_test',
        'class' => UserForTestObject::class
    ));

    protected $_test_validator;

    function setValidator($validator)
    {
        $this->_test_validator = $validator;
    }

    function _createInsertValidator()
    {
        if ($this->_test_validator)
            return $this->_test_validator;

        return parent::_createInsertValidator();
    }
}
