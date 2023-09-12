<?php
namespace Tests\cms\cases\Controllers;

use limb\active_record\src\lmbActiveRecord;

class ObjectForTesting extends lmbActiveRecord
{
    protected $_db_table_name = 'cms_object_for_testing';
}
