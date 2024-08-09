<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src\exception;

use limb\core\src\exception\lmbException;

/**
 * class lmbClassNotFoundException.
 *
 * @package log
 * @version $Id$
 */
class lmbClassNotFoundException extends lmbException
{
    function __construct($class_name, $message = 'class not found', $params = [])
    {
        $params['class_name'] = $class_name;

        parent::__construct($class_name . ': ' . $message, $params);
    }
}
