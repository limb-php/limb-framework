<?php

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\SocialSecurityForTestObject;

class PersonForTestObject extends lmbActiveRecord
{
    public $save_count = 0;
    protected $_has_one = array('social_security' => array('field' => 'ss_id',
        'class' => SocialSecurityForTestObject::class,
        'can_be_null' => true));

    function _onSave()
    {
        $this->save_count++;
    }
}