<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\rule;

/**
 * For fields have a minimum and maximum length
 */

/**
 * Checks that field has minimux and (or) maximum length
 * Example of usage:
 * <code>
 *  use limb\validation\rule\SizeRangeRule;
 *  // restricts "title" field to be more than 50 characters (minimum length is 0)
 *  $validator->addRule(new SizeRangeRule('title', 50));
 *  //same as above
 *  $validator->addRule(new lmbHandle('limb\validation\rule\SizeRangeRule', array('title', 10)));
 *  // checks that "title" field have length between 10 and 50 characters
 *  $validator->addRule(new SizeRangeRule('title', 10, 50));
 * </code>
 * @package validation
 * @version $Id: SizeRangeRule.php 7486 2009-01-26 19:13:20Z
 */
class SizeRangeRule extends lmbSingleFieldRule
{
    /**
     * @var int Minumum length
     */
    protected $min_length;
    /**
     * @var int Maximum length
     */
    protected $max_length;

    /**
     * Constructor
     * If only two agruments given - use second argument as maximun field length
     * If all three agruments given - use second argument as manimum field length and third - as maximum field length
     * @param string $field_name field name to validate
     * @param int $min_or_max_length Minumum or maximum length
     * @param int $max_length Maximum length (optional)
     */
    function __construct($field_name, $min_or_max_length, $max_length = null, $custom_error = null)
    {
        parent::__construct($field_name, $custom_error);

        if (is_null($max_length)) {
            $this->min_length = null;
            $this->max_length = $min_or_max_length;
        } else {
            $this->min_length = $min_or_max_length;
            $this->max_length = $max_length;
        }
    }

    function check($value)
    {
        if (!is_null($this->min_length) && (strlen($value) < $this->min_length)) {
            $this->error('{Field} must be greater than {min} characters.', array(
                'min' => $this->min_length,
                'max' => $this->max_length
            ));
        } elseif (strlen($value) > $this->max_length) {
            $this->error('{Field} must be less than {max} characters.', array(
                'max' => $this->max_length,
                'min' => $this->min_length
            ));
        }
    }
}
