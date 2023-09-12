<?php

namespace Tests\active_record\cases\src;

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
