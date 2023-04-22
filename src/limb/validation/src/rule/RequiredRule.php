<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\validation\src\rule;

use limb\i18n\src\lmbI18n;

/**
 * Checks that field is present in datasource and has not empty value
 * Example of usage:
 * <code>
 *  use limb\validation\src\rule\RequiredRule;
 *  $validator->addRule(new RequiredRule('title'));
 *  //or
 *  $validator->addRule(new lmbHandle('limb\validation\src\rule\RequiredRule', array('title')));
 *  // or
 *  $validator->addRequiredRule('title');
 * </code>
 * @see lmbValidator::addRequiredRule()
 * @package validation
 * @version $Id: RequiredRule.php 7486 2009-01-26
 */
class RequiredRule extends lmbBaseValidationRule
{
  /**
  * @var string Field name
  */
  protected $field_name;
  /**
  * @var string Custom error message
  */
  protected $custom_error;

  /**
  * Constructor
  * @param string $field_name Field name
  */
  function __construct($field_name, $custom_error = null)
  {
    $this->field_name = $field_name;
    $this->custom_error = $custom_error;
  }

  /**
  * @see lmbBaseValidationRule::_doValidate()
  */
  protected function _doValidate($datasource)
  {
    $value = $datasource[$this->field_name] ?? null;
    if(is_null($value) || (is_string($value) && trim($value) === ''))
    {
      $error = $this->custom_error ?? lmbI18n::translate('{Field} is required', 'validation');
      $this->error($error, array('Field' => $this->field_name));
    }
  }
}
