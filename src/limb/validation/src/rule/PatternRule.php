<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

/**
 * Checks that field value match some regexp.
 * In other words this rule triggers validation error if field value doesn't match regexp.
 * Example of usage:
 * <code>
 * use limb\validation\src\rule\PatternRule;
 * $validator->addRule(new PatternRule("title", "/^[a-zA-Z0-9.-]+$/i"));
 * </code>
 * @package validation
 * @version $Id: PatternRule.php 7486 2009-01-26 19:13:20Z
 */
class PatternRule extends lmbSingleFieldRule
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
