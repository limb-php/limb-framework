<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class LazyTestOneTableObject extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';
    protected $_lazy_attributes = array('annotation', 'content');
}
