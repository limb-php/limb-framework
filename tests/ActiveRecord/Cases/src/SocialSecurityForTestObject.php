<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class SocialSecurityForTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'social_security_for_test';

    protected $_belongs_to = array(
        'person' => array(
            'field' => 'ss_id',
            'class' => PersonForTestObject::class
        )
    );
}
