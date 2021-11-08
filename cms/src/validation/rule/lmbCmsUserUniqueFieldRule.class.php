<?php
namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\active_record\src\lmbActiveRecord;

class lmbCmsUserUniqueFieldRule extends lmbSingleFieldRule
{
  protected $user;
  protected $custom_error;

  function __construct($field, $user, $custom_error = '')
  {
    $this->user = $user;
    $this->custom_error = $custom_error;

    parent :: __construct($field);
  }

  function check($value)
  {
    $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
    if(!$this->user->isNew())
      $criteria->addAnd($this->user->getPrimaryKeyName() . ' <> '. $this->user->getId());

    if(lmbActiveRecord :: findOne(get_class($this->user), $criteria))
    {
      $error = $this->custom_error ? $this->custom_error : 'Пользователь со значением поля {Field} уже существует';
      $this->error( $error );
    }
  }
}

