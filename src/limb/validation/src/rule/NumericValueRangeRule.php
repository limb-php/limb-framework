<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

/**
 * Checks that field has minimux and (or) maximum length
 * Example of usage:
 * <code>
 *  use limb\validation\src\rule\NumericValueRangeRule;
 *  // restricts "length" field to have values less than 10.5 and more than 50
 *  $validator->addRule(new NumericValueRangeRule('length', 10.5, 50));
 * </code>
 * @package validation
 * @version $Id: NumericValueRangeRule.php 7486 2009-01-26 19:13:20Z
 */
class NumericValueRangeRule extends lmbSingleFieldRule
{
    /**
     * @var float Minimum allowed value
     */
    protected $min_value;
    /**
     * @var float Maximum allowed value
     */
    protected $max_value;

    /**
     * @param string $field_name Field name
     * @param float $min_value Min value
     * @param float $max_value Max value
     */
    function __construct($field_name, $min_value, $max_value, $custom_error = null)
    {
        parent::__construct($field_name, $custom_error);

        $this->min_value = $min_value;
        $this->max_value = $max_value;
    }

    function check($value)
    {
        if (!preg_match('/^[+-]?(\d*)$/', $value, $match)) {
            $this->error('{Field} must be a valid number.');
            return;
        }

        if ($value < $this->min_value) {
            $this->error('{Field} must be not less than {value}.', array('value' => $this->min_value));
        }

        if ($value > $this->max_value) {
            $this->error('{Field} must be not greater than {value}.', array('value' => $this->max_value));
        }
    }
}
