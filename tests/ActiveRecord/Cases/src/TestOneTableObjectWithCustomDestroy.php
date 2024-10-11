<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class TestOneTableObjectWithCustomDestroy extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';

    function destroy()
    {
        parent::destroy();
        echo "destroyed!";
    }
}
