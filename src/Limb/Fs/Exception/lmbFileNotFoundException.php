<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Fs\Exception;

/**
 * class lmbFileNotFoundException.
 *
 * @package fs
 * @version $Id$
 */
class lmbFileNotFoundException extends lmbFsException
{
    private $_file_path;

    function __construct($file_path, $message = 'file not found', $params = array())
    {
        $this->_file_path = $file_path;

        $params['file_path'] = $file_path;

        parent::__construct($file_path . ': ' . $message, $params);
    }

    function getFilePath()
    {
        return $this->_file_path;
    }
}
