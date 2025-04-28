<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

use limb\net\src\lmbHttpRequest;
use limb\validation\src\lmbErrorList;

/**
 * A base class for validation rules.
 * Implements Composite.
 * @package validation
 * @version $Id$
 */
abstract class lmbBaseValidationRule implements lmbValidationRuleInterface
{
    /**
     * @var boolean Flag if validation rule has an error or not
     */
    protected $is_valid = true;

    /**
     * @see validate()
     * @var lmbErrorList List of errors.
     */
    protected $error_list;

    /**
     * @var string Custom error message
     */
    protected $custom_error;

    function isValid(): bool
    {
        return $this->is_valid;
    }

    function setCustomError(string $custom_error): self
    {
        $this->custom_error = $custom_error;

        return $this;
    }

    /**
     * Adds an error to error list.
     * Sets "is_valid" flag to false.
     */
    function error($message, $fields = array(), $values = array()): void
    {
        $class_parts = explode('\\', get_called_class());
        $validatorName = end($class_parts);

        $message = $this->custom_error ?? $message;

        $this->error_list->addError($message, $fields, $values, $validatorName);
        $this->is_valid = false;
    }

    /**
     * Validates datasource
     * @see lmbValidationRule::validate
     */
    function validate($datasource, $error_list): bool
    {
        $this->error_list = $error_list;

        // for BC
        //if(is_array($datasource))
        //$datasource = new lmbObject($datasource);
        if (is_a($datasource, lmbHttpRequest::class))
            $datasource = $datasource->export();

        $this->_doValidate($datasource);

        return $this->is_valid;
    }

    abstract protected function _doValidate($datasource);
}
