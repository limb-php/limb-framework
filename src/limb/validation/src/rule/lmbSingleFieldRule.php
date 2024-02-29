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
 * A base class for rules responsible for validating a single field should inherit this class.
 * @package validation
 * @version $Id: lmbSingleFieldRule.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbSingleFieldRule extends lmbBaseValidationRule
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
     * @param string $field_name Field name
     * @param string|null $custom_error
     */
    function __construct($field_name, $custom_error = null)
    {
        $this->field_name = $field_name;
        $this->custom_error = $custom_error;
    }

    /**
     * @return string Field name
     */
    function getField()
    {
        return $this->field_name;
    }

    /**
     * Alias for adding single field error to error list
     * Fills field array with array('Field' => $this->field_name) that is ok for single field rules
     * If $custom_error attribute is set will use $custom_error regardless of $message
     * If $custom_error attribute is not set will apply lmb_i18n function to $message
     * @param string $message Error message
     * @param array $values Array of values
     * @return void
     * @see lmbErrorList::addError()
     */
    function error($message, $values = array(), $i18n_params = array()): void
    {
        $error = $this->custom_error ?? lmbI18n::translate($message, $i18n_params, 'validation');

        parent::error($error, array('Field' => $this->field_name), $values);
    }

    /**
     * Validates field
     * Calls {@link check()} method if $datasource has such field with not empty value.
     * Child classes must implement check($value) method to perform real validation.
     * To check field for existance and having none empty value use {@link RequiredRule}
     * Fills {@link $error_list}
     * @see lmbBaseValidationRule::_doValidate()
     */
    protected function _doValidate($datasource)
    {
        $value = $datasource[$this->field_name] ?? null;
        if (isset($value) && $value !== '')
            $this->check($value);
    }

    /**
     * Performs real validation
     * @param mixed $value Field value check
     * @return void
     */
    abstract function check($value);
}
