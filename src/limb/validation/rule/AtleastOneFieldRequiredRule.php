<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\rule;

use limb\i18n\lmbI18n;

/**
 * Checks that at least one field from a list has not null value
 * Example of usage:
 * <code>
 * use limb\validation\rule\AtleastOneFieldRequiredRule;
 * $validator->addRule(new AtleastOneFieldRequiredRule(array('name', 'nickname', 'full_name')));
 * </code>
 * @package validation
 * @version $Id: AtleastOneFieldRequiredRule.php 7486 2009-01-26 19:13:20Z
 */
class AtleastOneFieldRequiredRule extends lmbBaseValidationRule
{
    /**
     * @var array List of fields
     */
    protected $field_names;
    /**
     * @var string Custom error message
     */
    protected $custom_error;

    function __construct($field_names, $custom_error = null)
    {
        $this->field_names = $field_names;
        $this->custom_error = $custom_error;
    }

    /**
     * @see lmbBaseValidationRule::_doValidate()
     */
    protected function _doValidate($datasource)
    {
        if (!$this->_findAtleastOneField($datasource)) {
            $error = $this->custom_error ?? $this->_generateErrorMessage();
            $this->error($error, $this->field_names, array());
        }
    }

    protected function _findAtleastOneField($datasource)
    {
        foreach ($this->field_names as $field_name) {
            if ($value = $datasource->get($field_name))
                return true;
        }

        return false;
    }

    protected function _generateErrorMessage()
    {
        for ($i = 0; $i < count($this->field_names); $i++)
            $fields[] = '{' . $i . '}';

        return lmbI18n::translate('Atleast one field required among: {fields}',
            array('{fields}' => implode(', ', $fields)),
            'validation');
    }
}
