<?php

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;

class TestOneTableObjectWithCustomDestroy extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';

    function destroy()
    {
        parent::destroy();
        echo "destroyed!";
    }
}