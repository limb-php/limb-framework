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
 * Checks that field is not in a list of restricted values
 * Example of usage:
 * <code>
 *  use limb\validation\rule\NotInArrayRule;
 *  $validator->addRule(new NotInArrayRule('login', array('www', 'mail', 'smtp')));
 * </code>
 * @package validation
 * @version $Id$
 */
class NotInArrayRule extends lmbSingleFieldRule
{
    /**
     * @var array A list of not allowed values
     */
    protected $restricted_values = array();

    /**
     * Constructor.
     * @param string $field_name Field name
     * @param array $restricted_values List of restricted values
     * @param string|null $custom_error Custom error message
     */
    function __construct($field_name, $restricted_values, $custom_error = null)
    {
        parent::__construct($field_name, $custom_error);

        $this->restricted_values = $restricted_values;
    }

    function check($value)
    {
        if (in_array($value, $this->restricted_values))
            $this->error('{Field} has not allowed value.');
    }
}
