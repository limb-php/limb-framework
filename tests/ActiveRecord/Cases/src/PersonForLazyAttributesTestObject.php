<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class PersonForLazyAttributesTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'person_for_test';
    protected $_has_one = array(
        'lazy_object' => array(
            'field' => 'ss_id',
            'class' => LazyTestOneTableObject::class,
            'can_be_null' => true));

    protected $_lazy_attributes = array('name');
}
