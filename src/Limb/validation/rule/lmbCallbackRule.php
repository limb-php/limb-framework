<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\rule;

use limb\core\lmbDelegate;

/**
 * Validation rule that accepts callback in constructor and executes that callback in validate() method
 * Callback must follow lmbValidationRule interface.
 * Callback should return validation result that will be used as a value for lmbBaseValidationRule::$is_valid property.
 * @package validation
 * @version $Id$
 */
class lmbCallbackRule extends lmbBaseValidationRule
{
    protected $callback;

    function __construct($object, $method = '')
    {
        $this->callback = new lmbDelegate($object, $method);
    }

    /**
     * @see lmbBaseValidationRule::_doValidate()
     */
    protected function _doValidate($datasource)
    {
        $this->is_valid = $this->callback->invoke($datasource, $this->error_list);
    }
}
