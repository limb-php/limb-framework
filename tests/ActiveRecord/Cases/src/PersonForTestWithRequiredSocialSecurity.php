<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;
use limb\validation\lmbValidator;

class PersonForTestWithRequiredSocialSecurity extends lmbActiveRecord
{
    protected $_db_table_name = 'person_for_test';
    protected $_has_one = array('social_security' => array(
        'field' => 'ss_id',
        'class' => SocialSecurityForTestObject::class,
        'can_be_null' => true
    ));

    function _createValidator()
    {
        $validator = new lmbValidator();
        $validator->addRequiredObjectRule('social_security');
        return $validator;
    }
}
