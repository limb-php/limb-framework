<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\validation\src\rule;

use limb\net\src\lmbHttpRequest;
use limb\validation\src\lmbErrorList;

/**
 * A base class for validation rules.
 * Implements Composite.
 * @package validation
 * @version $Id$
 */
abstract class lmbBaseValidationRule implements lmbValidationRuleInterface
{
  /**
  * @var boolean Flag if validation rule has an error or not
  */
  protected $is_valid = true;

  /**
  * @see validate()
  * @var lmbErrorList List of errors.
  */
  protected $error_list;

  function isValid(): bool
  {
    return $this->is_valid;
  }

  /**
  * Adds an error to error list.
  * Sets "is_valid" flag to false.
  */
  function error($message, $fields = array(), $values = array())
  {
      $class_parts = explode('\\', get_called_class());
      $validatorName = end($class_parts);

      $this->error_list->addError($message, $fields, $values, $validatorName);
      $this->is_valid = false;
  }

  /**
  * Validates datasource
  * @see lmbValidationRule::validate
  */
  function validate($datasource, $error_list)
  {
    $this->error_list = $error_list;

    // for BC
    //if(is_array($datasource))
        //$datasource = new lmbObject($datasource);
      if(is_a($datasource, lmbHttpRequest::class))
          $datasource = $datasource->export();

    $this->_doValidate($datasource);

    return $this->is_valid;
  }

  abstract protected function _doValidate($datasource);
}
