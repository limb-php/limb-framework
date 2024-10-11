<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class TestOneTableObject2 extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';

    public function getFooBar()
    {
        return 'foo_bar';
    }
}
