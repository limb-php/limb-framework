<?php
namespace limb\cms\src\validation\rule;

use limb\active_record\src\lmbActiveRecord;
use limb\validation\src\rule\lmbSingleFieldRule;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;

class lmbCmsTextBlockUniqueFieldRule extends lmbSingleFieldRule
{
  protected $text_block_class;
  protected $text_block;

  function __construct($field_name, $text_block_class, $text_block = null, $custom_error = null)
  {
    $this->text_block_class = $text_block_class;
    $this->text_block = is_object($text_block) ? $text_block : new $text_block();

    parent::__construct($field_name, $custom_error);
  }

  function check($value)
  {
    $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
    $criteria->addAnd(new lmbSQLFieldCriteria($this->text_block->getPrimaryKeyName(), $this->text_block->getId(), lmbSQLFieldCriteria::NOT_EQUAL));

    if(lmbActiveRecord::findFirst($this->text_block_class, $criteria))
      $this->error('Text block with field {Field} already exist');
  }
}
