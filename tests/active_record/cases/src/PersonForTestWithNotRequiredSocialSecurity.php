<?php

namespace Tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class PersonForTestWithNotRequiredSocialSecurity extends lmbActiveRecord
{
    protected $_db_table_name = 'person_for_test';
    protected $_has_one = array('social_security' => array(
        'field' => 'ss_id',
        'class' => SocialSecurityForTestObject::class,
        'can_be_null' => true,
        'throw_exception_on_not_found' => false));
}
