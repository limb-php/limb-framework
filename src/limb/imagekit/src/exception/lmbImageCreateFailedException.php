<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\exception;

use limb\core\src\exception\lmbException;

/**
 * Exception 'Image create is failed'
 *
 * @package imagekit
 * @version $Id: lmbImageCreateFailedException.php 7486 2009-01-26 19:13:20Z
 */
class lmbImageCreateFailedException extends lmbException
{

    function __construct($file_name, $type = '')
    {
        parent::__construct('Image create is failed', array('file' => $file_name, 'type' => $type));
    }

}
