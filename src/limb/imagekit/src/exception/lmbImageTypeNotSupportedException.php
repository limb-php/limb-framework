<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\exception;

use limb\core\src\exception\lmbException;

/**
 * Exception 'Image type is not supported'
 *
 * @package imagekit
 * @version $Id: lmbImageTypeNotSupportedException.php 7486 2009-01-26 19:13:20Z
 */
class lmbImageTypeNotSupportedException extends lmbException
{

    function __construct($type = '')
    {
        parent::__construct('Image type is not supported', $type ? array('type' => $type) : array());
    }

}
