<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\validation\src\rule;

/**
 * Interface for defining rules to validate against
 * @package validation
 * @version $Id: lmbValidationRuleInterface.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
interface lmbValidationRuleInterface
{
  /**
  * Performs validation
  * Validation rules must call {@link lmbErrorList :: addError()} to report about error
  * @see lmbErrorList :: addError()
  * @param lmbSetInterface Datasource to validate
  * @param lmbErrorList List of validation errors
  * @return void
  */
  function validate($datasource, $error_list);
}

