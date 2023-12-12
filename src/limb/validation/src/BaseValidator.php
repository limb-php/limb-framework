<?php

namespace limb\validation\src;

abstract class BaseValidator
{
    protected $data = array();
    protected $error_list;

    public function __construct($error_list = null)
    {
        $this->error_list = $error_list ?? new lmbErrorList();
    }

    public function getErrorList($rename_fields = array())
    {
        if (!empty($rename_fields))
            $this->error_list->renameFields($rename_fields);

        return $this->error_list;
    }

    protected function _createValidator(): lmbValidator
    {
        $validator = new lmbValidator();

        return $validator;
    }

    protected function merge(array $added_data)
    {
        $this->data = array_merge($this->data, $this->parseData($added_data));
    }

    public function validate($datasource)
    {
        $this->merge($datasource);

        $validator = $this->_createValidator();
        $validator->setErrorList($this->error_list);
        $validator->validate($this->data);

        return $validator->isValid();
    }

    public function isValid()
    {
        return $this->error_list->isValid();
    }

    public function parseData($data)
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->parseData($value);
            }

            $newData[$key] = $value;
        }

        return $newData;
    }
}
