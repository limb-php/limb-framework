<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

use limb\i18n\src\lmbI18n;

/**
 * Checks that field value exactly match a reference field value
 * Example of usage:
 * <code>
 *  use limb\validation\src\rule\MatchRule;
 *  $validator->addRule(new MatchRule('password', 'repeat_password'));
 * </code>
 * @package validation
 * @version $Id: MatchRule.php 7486 2009-01-26 19:13:20Z
 */
class MatchRule extends lmbBaseValidationRule
{
    /**
     * @var string Reference field name to match against
     */
    protected $reference_field;

    /**
     * @var string Field name to validate
     */
    protected $field_name;
    /**
     * @var string Custom error message
     */
    protected $custom_error;

    /**
     * Constructor
     * @param string $field_name Field name to validate
     * @param string $reference_field Reference field name in datasource to match against
     */
    function __construct($field_name, $reference_field, $custom_error = null)
    {
        $this->field_name = $field_name;
        $this->reference_field = $reference_field;
        $this->custom_error = $custom_error;
    }

    /**
     * @see lmbBaseValidationRule::_doValidate()
     */
    protected function _doValidate($datasource)
    {
        $value1 = $datasource[$this->field_name] ?? null;
        $value2 = $datasource[$this->reference_field] ?? null;

        if (isset($value1) && isset($value2) && strcmp($value1, $value2)) {
            $error = $this->custom_error ?? lmbI18n::translate('{Field} does not match {MatchField}.', 'validation');
            $this->error($error, array('Field' => $this->field_name, 'MatchField' => $this->reference_field));
        }
    }
}
