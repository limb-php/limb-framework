<?php

namespace Tests\cms\cases\Controllers;

use limb\active_record\src\lmbActiveRecord;
use limb\validation\src\lmbValidator;

class AdminObjectForTesting extends lmbActiveRecord
{
    protected $_db_table_name = 'cms_object_for_testing';

    protected function _createValidator(): lmbValidator
    {
        $validator = new lmbValidator();
        $validator->addRequiredRule('field');
        return $validator;
    }
}
