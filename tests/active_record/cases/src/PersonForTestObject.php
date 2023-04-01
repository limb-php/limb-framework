<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class PersonForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'person_for_test';

    public $save_count = 0;
    protected $_has_one = array('social_security' => array('field' => 'ss_id',
        'class' => SocialSecurityForTestObject::class,
        'can_be_null' => true));

    function _onSave()
    {
        $this->save_count++;
    }
}
