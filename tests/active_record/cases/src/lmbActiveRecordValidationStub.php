<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;
use limb\validation\src\lmbValidator;

class lmbActiveRecordValidationStub extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';
    protected $_insert_validator;
    protected $_update_validator;

    function setInsertValidator($validator)
    {
        $this->_insert_validator = $validator;
    }

    function setUpdateValidator($validator)
    {
        $this->_update_validator = $validator;
    }

    protected function _createInsertValidator()
    {
        return is_object($this->_insert_validator) ? $this->_insert_validator : new lmbValidator();
    }

    protected function _createUpdateValidator()
    {
        return is_object($this->_update_validator) ? $this->_update_validator : new lmbValidator();
    }
}
