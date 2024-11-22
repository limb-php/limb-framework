<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

/**
 * Checks that field value is not equal some invalid value
 * Example of usage:
 * <code>
 * use limb\validation\src\rule\InvalidValueRule;
 * $validator->addRule(new InvalidValueRule("region", -1));
 * </code>
 * @package validation
 * @version $Id: InvalidValueRule.php 7486 2009-01-26 19:13:20Z
 */
class InvalidValueRule extends lmbSingleFieldRule
{
    protected $invalid_value;

    function __construct($field_name, $invalid_value, $custom_error = null)
    {
        parent::__construct($field_name, $custom_error);

        $this->invalid_value = $invalid_value;
    }

    function check($value)
    {
        $invalid_value = $this->invalid_value;

        settype($invalid_value, 'string');//???

        if ($value == $invalid_value) {
            $this->error('{Field} value is wrong');
        }
    }
}
