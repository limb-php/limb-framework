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
 * Checks that field value is a valid numeric value and its precision falls
 * within allowable parameters.
 * Example of usage:
 * <code>
 *  use limb\validation\src\rule\NumericPrecisionRule;
 *  $validator->addRule(new NumericPrecisionRule('price', 5, 2));
 *  // 100.2 with match this rule, 100.300 or 100000 - not.
 * </code>
 * @package validation
 * @version $Id: NumericPrecisionRule.php 7486 2009-01-26 19:13:20Z
 */
class NumericPrecisionRule extends lmbSingleFieldRule
{
    /**
     * @var int Number of decimal digits allowed
     */
    protected $decimal_digits;
    /**
     * @var int Number of whole digits allowed
     */
    protected $whole_digits;

    /**
     * @param string $field_name Field name
     * @param int $whole_digits Number of whole digits allowed
     * @param int $decimal_digits Number of decimal digits allowed
     */
    function __construct($field_name, $whole_digits, $decimal_digits = 0, $custom_error = null)
    {
        parent::__construct($field_name, $custom_error);

        $this->whole_digits = $whole_digits;
        $this->decimal_digits = $decimal_digits;
    }

    function check($value)
    {
        if (preg_match('/^[+-]?(\d*)\.?(\d*)$/', $value, $match)) {
            if (strlen($match[1]) > $this->whole_digits) {
                $this->error('You have entered too many whole digits ({digits}) in {Field} (max {maxdigits}).',
                    array('maxdigits' => $this->whole_digits,
                        'digits' => strlen($match[1])));
            }

            if (strlen($match[2]) > $this->decimal_digits) {
                $this->error('You have entered too many decimal digits ({digits}) in {Field} (max {maxdigits}).',
                    array('maxdigits' => $this->decimal_digits,
                        'digits' => strlen($match[2])));
            }
        } else {
            $this->error('{Field} must be a valid number.');
        }
    }
}
