<?php

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\PersonForTestObject;

class SocialSecurityForTestObject extends lmbActiveRecord
{
    protected $_belongs_to = array('person' => array('field' => 'ss_id',
        'class' => PersonForTestObject::class
    ));
}