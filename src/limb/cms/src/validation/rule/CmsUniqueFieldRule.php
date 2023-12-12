<?php

namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\active_record\src\lmbActiveRecord;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;

class CmsUniqueFieldRule extends lmbSingleFieldRule
{
    protected $model_class;
    protected $ignore;

    function __construct($field, $model_class, $object = null, $custom_error = null)
    {
        if (is_object($model_class)) {
            $this->ignore = $model_class;
            $this->model_class = get_class($model_class);
            $custom_error = $object;
        } else {
            $this->ignore = $object;
            $this->model_class = $model_class;
        }

        parent::__construct($field, $custom_error);
    }

    function check($value)
    {
        $criteria = lmbSQLCriteria::equal($this->field_name, $value);
        $criteria->addAnd(new lmbSQLFieldCriteria($this->ignore->getPrimaryKeyName(), $this->ignore->getId(), lmbSQLFieldCriteria::NOT_EQUAL));

        if (lmbActiveRecord::findFirst($this->model_class, $criteria)) {
            $this->error('Field {Field} already exists');
        }
    }
}
