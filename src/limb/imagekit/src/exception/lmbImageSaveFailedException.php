<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\exception;

use limb\core\src\exception\lmbException;

/**
 * Exception 'Image save is failed'
 *
 * @package imagekit
 * @version $Id: lmbImageSaveFailedException.php 7486 2009-01-26 19:13:20Z
 */
class lmbImageSaveFailedException extends lmbException
{

    function __construct($file_name)
    {
        parent::__construct('Image save is failed', array('file' => $file_name));
    }

}
