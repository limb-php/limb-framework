<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class LazyTestOneTableObject extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';
    protected $_lazy_attributes = array('annotation', 'content');
}
