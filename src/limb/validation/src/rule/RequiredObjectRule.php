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
 * Checks that field is present in datasource and it's value is an object of some or any class
 * Example of usage:
 * <code>
 *  use limb\validation\src\rule\RequiredObjectRule;
 *  $validator->addRule(new RequiredObjectRule('author', 'Author'));
 *  // or
 *  $validator->addRequiredObjectRule('folder');
 * </code>
 * @see lmbValidator::addRequiredObjectRule()
 * @package validation
 * @version $Id: RequiredObjectRule.php 7486 2009-01-26 19:13:20Z
 */
class RequiredObjectRule extends lmbBaseValidationRule
{
    /**
     * @var string Field name
     */
    protected $field_name;
    /**
     * @var string Required class name
     */
    protected $class;
    /**
     * @var string Custom error message
     */
    protected $custom_error;

    /**
     * @param string $field_name Field name
     * @param string $class Required class name
     */
    function __construct($field_name, $class = null, $custom_error = null)
    {
        $this->field_name = $field_name;
        $this->class = $class;
        $this->custom_error = $custom_error;
    }

    /**
     * @see lmbBaseValidationRule::_doValidate()
     */
    protected function _doValidate($datasource)
    {
        $value = $datasource[$this->field_name];

        if (!is_object($value) || ($this->class && !($value instanceof $this->class))) {
            $error = $this->custom_error ?? lmbI18n::translate('Object {Field} is required', 'validation');
            $this->error($error, array('Field' => $this->field_name));
        }
    }
}
