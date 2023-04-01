<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class TestOneTableObject2 extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';

    public function getFooBar()
    {
        return 'foo_bar';
    }
}
