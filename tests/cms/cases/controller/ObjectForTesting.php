<?php
namespace tests\cms\cases\controller;

use limb\active_record\src\lmbActiveRecord;

class ObjectForTesting extends lmbActiveRecord
{
    protected $_db_table_name = 'cms_object_for_testing';
}
