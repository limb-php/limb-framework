<?php

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use tests\active_record\cases\LazyTestOneTableObject;

class PersonForLazyAttributesTestObject extends lmbActiveRecord
{
    protected $_db_table_name = 'person_for_test';
    protected $_has_one = array('lazy_object' => array('field' => 'ss_id',
        'class' => LazyTestOneTableObject::class,
        'can_be_null' => true));

    protected $_lazy_attributes = array('name');
}