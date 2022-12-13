<?php
namespace limb\validation\src;

class BaseARValidator extends BaseValidator
{
    protected $model_class;
    protected $ignore;

    public function __construct($model_class, $error_list = null)
    {
        $this->model_class = $model_class;

        parent::__construct($error_list);
    }

    public function ignore($ignore)
    {
        $this->ignore = $ignore;

        return $this;
    }

    protected function _createInsertValidator(): lmbValidator
    {
        $validator = $this->_createValidator();

        return $validator;
    }

    public function validate($datasource, $is_create = false)
    {
        $this->merge($datasource);

        if( $is_create )
            $validator = $this->_createInsertValidator();
        else
            $validator = $this->_createValidator();
        $validator->setErrorList($this->error_list);
        $validator->validate($this->data);

        return $validator->isValid();
    }
}
