<?php
/*
 * Limb PHP Framework
 *
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
