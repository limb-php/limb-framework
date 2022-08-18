<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\validation\src\rule;

use limb\validation\src\rule\lmbSingleFieldRule;

/**
 * Checks that field is not not in a list of restricted values 
 * Example of usage:
 * <code>
 *  use limb/validation/src/rule/lmbNotInArrayRule;
 *  $validator->addRule(new lmbMatchRule('login', array('www', 'mail', 'smtp')));
 * </code>
 * @package validation
 * @version $Id$
 */
class lmbNotInArrayRule extends lmbSingleFieldRule
{
  /**
  * @var array A list of not allowed values
  */
  protected $restricted_values = array();

  /**
  * Constructor.
  * @param string Field name
  * @param array List of restricted values
  * @param string Custom error message
  */   
  function __construct($field_name, $restricted_values, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);
    
    $this->restricted_values = $restricted_values;
  }

  function check($value)
  { 
    if(in_array($value, $this->restricted_values))
      $this->error('{Field} has not allowed value.');
  }
}

