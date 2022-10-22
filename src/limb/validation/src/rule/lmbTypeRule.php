<?php
namespace limb\validation\src\rule;

use limb\core\src\exception\lmbInvalidArgumentException;
use limb\core\src\lmbAssert;

class lmbTypeRule extends lmbSingleFieldRule
{
  protected $type;

  function __construct($field_name, $type, $custom_error = '{Field} must contain only integer values')
  {
    $this->type = $type;
    parent::__construct($field_name, $custom_error);
  }

  function check($value)
  {
    try
    {
      lmbAssert::assert_type($value, $this->type);
    }
    catch (lmbInvalidArgumentException $e)
    {
      $this->error($this->custom_error);
    }
  }
}
