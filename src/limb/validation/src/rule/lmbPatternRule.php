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
 * Checks that field value match some regexp.
 * In other words this rule triggers validation error if field value doesn't match regexp.
 * Example of usage:
 * <code>
 * use limb\validation\src\rule\lmbPatternRule;
 * $validator->addRule(new lmbPatternRule("title", "/^[a-zA-Z0-9.-]+$/i"));
 * </code>
 * @package validation
 * @version $Id: lmbPatternRule.php 7486 2009-01-26 19:13:20Z
 */
class lmbPatternRule extends lmbSingleFieldRule
{
  /**
   * @var string Pattern to match with
   */
  protected $pattern;

  function __construct($field_name, $pattern, $custom_error = null)
  {
    parent::__construct($field_name, $custom_error);

    $this->pattern = $pattern;
  }

  function check($value)
  {
    if (!preg_match($this->pattern, $value))
      $this->error('{Field} value is wrong');
  }
}
