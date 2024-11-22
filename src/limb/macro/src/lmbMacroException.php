<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src;

use limb\core\src\exception\lmbException;

/**
 * class lmbMacroException.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroException extends lmbException
{
    function __construct($message, $params = array())
    {
        parent::__construct('MACRO exception: ' . $message, $params);
    }
}
