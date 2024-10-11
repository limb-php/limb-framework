<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class TestOneTableObjectFailing extends lmbActiveRecord
{
    var $fail;
    protected $_db_table_name = 'test_one_table_object';

    protected function _onAfterSave()
    {
        if (is_object($this->fail))
            throw $this->fail;
    }
}
