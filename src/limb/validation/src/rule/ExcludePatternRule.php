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
 * Checks that field value doesn't match some regexp.
 * In other words this rule triggers validation error if field value matches regexp.
 * Example of usage:
 * <code>
 * use limb/validation/src/rule/ExcludePatternRule;
 * $validator->addRule(new ExcludePatternRule("title", "/[^a-zA-Z0-9.-]+/i"));
 * </code>
 * @package validation
 * @version $Id: ExcludePatternRule.php 7486 2009-01-26 19:13:20Z
 */
class ExcludePatternRule extends lmbSingleFieldRule
{
    /**
     * @var string Pattern to match against
     */
    protected $pattern;

    /**
     * @param string $field_name Field name
     * @param string $pattern Pattern to match against
     */
    function __construct($field_name, $pattern, $custom_error = null)
    {
        parent::__construct($field_name, $custom_error);

        $this->pattern = $pattern;
    }

    function check($value)
    {
        if (preg_match($this->pattern, $value)) {
            $this->error('{Field} value is wrong');
        }
    }
}
