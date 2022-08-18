<?php
namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbBaseValidationRule;

class lmbCmsTreeIdentifierRule extends lmbBaseValidationRule
{
  protected $field_name;
  protected $parent_node_id_field_name;
  protected $custom_error;

  /**
  * Constructor
  * @param string Field name
  * @param string custom error
  */
  function __construct($field_name, $custom_error = '')
  {
    $this->field_name = $field_name;
    $this->custom_error = $custom_error;
  }

  protected function _doValidate($datasource)
  {
    $identifier = $datasource[$this->field_name];

    if(!$this->check_identifier($identifier))
      return;

    if(!$parent_node = $datasource->getParent())
    {
      $error = $this->custom_error ? $this->custom_error : lmb_i18n('Parent node not found', 'cms');
      return;
    }

    if(!$nodes = $parent_node->getChildren())
      return;

    foreach($nodes as $node)
    {
      if($node->identifier != $identifier)
        continue;

      if($node->id == $datasource['id'])
        continue;

      $error = $this->custom_error ? $this->custom_error : lmb_i18n('Duplicate tree identifier', 'cms');
      $this->error($error, array('Field' => $this->field_name));
      break;
    }
  }

  protected function check_identifier($value)
  {
    if(!preg_match('~^[a-zA-Z0-9-_\.]+$~', $value))
    {
      $error = $this->custom_error ? $this->custom_error : lmb_i18n('{Field} can contain numeric, latin alphabet and `-`, `_`, `.` symbols only', 'cms');
      $this->error($error, array('Field' => $this->field_name));
      return;
    }

    return true;
  }
}

