<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src;

use limb\core\src\lmbHandle;
use limb\validation\src\rule\RequiredRule;
use limb\validation\src\rule\AtleastOneFieldRequiredRule;
use limb\validation\src\rule\RequiredObjectRule;
use limb\validation\src\rule\SizeRangeRule;

/**
 * Holds the list of validation rules along with errors happened during validation.
 * Validates a datasource against added validation rules.
 * @package validation
 * @version $Id: lmbValidator.php 7486 2009-01-26
 */
class lmbValidator
{
    /**
     * @see lmbValidationRule
     * @var array List of added validation rules
     */
    protected $rules = [];

    /**
     * @var array List of field names
     */
    protected $real_field_names = [];

    /**
     * @var lmbErrorList List of validation errors
     */
    protected $error_list;

    /**
     * Constructor
     * @param lmbErrorList|null $error_list
     */
    function __construct($error_list = null)
    {
        $this->error_list = $error_list;
    }

    /**
     * Returns list of errors.
     * Creates an empty lmbErrorList if error list is NULL
     * @return lmbErrorList
     */
    function getErrorList(): lmbErrorList
    {
        if (!$this->error_list)
            $this->error_list = new lmbErrorList();

        return $this->error_list;
    }

    /**
     * Sets new list of errors
     * @return void
     */
    function setErrorList($error_list)
    {
        $this->error_list = $error_list;
    }

    function setRealFieldNames($real_field_names)
    {
        $this->real_field_names = $real_field_names;
    }

    /**
     * Adds a new rule
     * @return void
     */
    function addRule($rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * Alias for adding lmbRequiredRule to validator
     * @return void
     */
    function addRequiredRule($field, $custom_error = null)
    {
        $this->addRule(new lmbHandle(RequiredRule::class,
            array($field, $custom_error)));
    }

    function addAtLeastOneRequiredRule($fields, $custom_error = null)
    {
        $this->addRule(new lmbHandle(AtleastOneFieldRequiredRule::class,
            array($fields, $custom_error)));
    }

    /**
     * Alias for adding lmbRequiredObjectRule to validator
     * @return void
     */
    function addRequiredObjectRule($field, $class = null, $custom_error = null)
    {
        $this->addRule(new lmbHandle(RequiredObjectRule::class,
            array($field, $class, $custom_error)));
    }

    /**
     * Alias for adding lmbSizeRangeRule to validator
     * @return void
     */
    function addSizeRangeRule($field, $min_or_max_length, $max_length = null, $custom_error = null)
    {
        $this->addRule(new lmbHandle(SizeRangeRule::class,
            array($field, $min_or_max_length, $max_length, $custom_error)));
    }

    /**
     * @return boolean TRUE if list of errors is empty
     */
    function isValid(): bool
    {
        return $this->getErrorList()->isValid();
    }

    /**
     * Performs validation
     * Passes datasource and list of errors to every validation rule
     * @param \limb\core\src\lmbSetInterface $datasource Datasource to validate
     * @return boolean True if valid
     */
    function validate($datasource): bool
    {
        foreach ($this->rules as $rule)
            $rule->validate($datasource, $this->getErrorList());
        $this->getErrorList()->renameFields($this->real_field_names);

        return $this->isValid();
    }
}
