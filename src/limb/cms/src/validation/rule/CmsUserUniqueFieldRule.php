<?php

namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\active_record\src\lmbActiveRecord;
use limb\i18n\src\lmbI18n;

class CmsUserUniqueFieldRule extends lmbSingleFieldRule
{
    protected $model_class;
    protected $ignore_user;

    function __construct($field_name, $model_class, $ignore_user = null, $custom_error = null)
    {
        if (is_object($model_class)) { // for BC
            $this->ignore_user = $model_class;
            $this->model_class = get_class($model_class);
            $custom_error = $ignore_user;
        } else {
            $this->ignore_user = $ignore_user;
            $this->model_class = $model_class;
        }

        parent::__construct($field_name, $custom_error);
    }

    function check($value)
    {
        $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
        if($this->ignore_user)
            $criteria->addAnd(new lmbSQLFieldCriteria($this->ignore_user->getPrimaryKeyName(), $this->ignore_user->getId(), lmbSQLFieldCriteria::NOT_EQUAL));

        if (lmbActiveRecord::findFirst($this->model_class, $criteria)) {
            $error = $this->custom_error ?? lmbI18n::translate('User with {Field} already exists', 'cms');
            $this->error($error);
        }
    }
}
