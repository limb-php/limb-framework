<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\exception;

use limb\core\exception\lmbException;

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
