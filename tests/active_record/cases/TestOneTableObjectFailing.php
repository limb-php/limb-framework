<?php

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;

class TestOneTableObjectFailing extends lmbActiveRecord
{
    var $fail;
    protected $_db_table_name = 'test_one_table_object';

    protected function _onAfterSave()
    {
        if(is_object($this->fail))
            throw $this->fail;
    }
}