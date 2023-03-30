<?php

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;

class TestOneTableObject extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';
}