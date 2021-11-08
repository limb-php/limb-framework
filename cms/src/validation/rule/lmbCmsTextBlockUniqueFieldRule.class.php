<?php
namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\cms\src\model\lmbCmsTextBlock;

class lmbCmsTextBlockUniqueFieldRule extends lmbSingleFieldRule
{
  protected $text_block;

  function __construct($field_name, $text_block)
  {
    $this->text_block = $text_block;
    parent :: __construct($field_name);
  }

  function check($value)
  {
    $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
    if($this->text_block->getId())
      $criteria->addAnd('id <> '. $this->text_block->getId());

    if(lmbCmsTextBlock :: findOne($criteria))
      $this->error('Тектовый блок со значением поля {Field} уже существует');
  }
}


