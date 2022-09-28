<?php
namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\active_record\src\lmbActiveRecord;
use limb\i18n\src\lmbI18n;

class lmbCmsUserUniqueFieldRule extends lmbSingleFieldRule
{
  protected $user;
  protected $custom_error;

  function __construct($field, $user, $custom_error = '')
  {
    $this->user = $user;
    $this->custom_error = $custom_error;

    parent::__construct($field);
  }

  function check($value)
  {
    $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
    if(!$this->user->isNew())
      $criteria->addAnd($this->user->getPrimaryKeyName() . ' <> '. $this->user->getId());

    if(lmbActiveRecord::findOne(get_class($this->user), $criteria, $this->user->getConnection()))
    {
      $error = $this->custom_error ?? lmbI18n::translate('User with {Field} already exists', 'cms');
      $this->error( $error );
    }
  }
}
