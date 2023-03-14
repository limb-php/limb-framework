<?php

namespace tests\cms\cases\controller;

use limb\active_record\src\lmbActiveRecord;
use limb\validation\src\lmbValidator;

class AdminObjectForTesting extends lmbActiveRecord
{
    protected $_db_table_name = 'cms_object_for_testing';

    protected function _createValidator()
    {
        $validator = new lmbValidator();
        $validator->addRequiredRule('field');
        return $validator;
    }
}
