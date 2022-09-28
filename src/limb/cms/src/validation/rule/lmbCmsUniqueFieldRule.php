<?php
namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\active_record\src\lmbActiveRecord;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;

class lmbCmsUniqueFieldRule extends lmbSingleFieldRule
{
  protected $class;
  protected $object;

  function __construct($field, $class, $object = null, $custom_error = '')
  {
    if( is_object($class) )
    {
      $this->object = $class;
      $this->class = get_class($class);
      $custom_error = $object;
    }
    else
    {
      $this->object = $object;
      $this->class = $class;
    }

    parent::__construct($field, $custom_error);
  }

  function check($value)
  {
    $criteria = lmbSQLCriteria::equal($this->field_name, $value);
    if(!$this->object->isNew())
      $criteria->addAnd(new lmbSQLFieldCriteria($this->object->getPrimaryKeyName(), $this->object->getId(), lmbSQLFieldCriteria::NOT_EQUAL));

    $records = lmbActiveRecord::find($this->class, $criteria, $this->object->getConnection());

    if($records->count())
      $this->error('Field {Field} already exists');
  }
}
