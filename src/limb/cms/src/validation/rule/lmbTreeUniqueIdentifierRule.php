<?php
namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\active_record\src\lmbActiveRecord;

class lmbTreeUniqueIdentifierRule extends lmbSingleFieldRule
{
  protected $field_name;
  protected $node;
  protected $error_message;
  protected $parent_id;

  function __construct($field_name, $node, $error_message, $parent_id = false)
  {
    $this->node = is_object($node) ? $node : new $node();
    $this->field_name = $field_name;
    $this->error_message = $error_message;
    $this->parent_id = $parent_id ?? $this->node->getParent()->getId();

    parent::__construct($field_name);
  }

  function check($value)
  {
    $criteria = lmbSQLCriteria::equal($this->field_name, $value)->addAnd('parent_id = ' . $this->parent_id);

    if(!$this->node->isNew())
      $criteria->addAnd($this->node->getPrimaryKeyName() . ' <> '. $this->node->getId());

    if(lmbActiveRecord::findFirst(get_class($this->node), $criteria))
      $this->error($this->error_message);
  }
}
