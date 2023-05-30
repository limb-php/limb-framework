<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

use limb\core\src\lmbObject;

/**
 * class lmbUploadedFile.
 *
 * @package net
 * @version $Id$
 *
 * @method string getName()
 * @method int getSize()
 * @method string getError()
 */
class lmbUploadedFile extends lmbObject
{
    public $name;
    public $error;
    public $type;
    public $size;
    public $tmp_name;

    function getFilePath()
    {
        return $this->getTmpName();
    }

    function getMimeType()
    {
        return $this->getType();
    }

    function move($dest)
    {
        return move_uploaded_file($this->getTmpName(), $dest);
    }

    function isUploaded()
    {
        return is_uploaded_file($this->getTmpName());
    }

    function isValid()
    {
        return $this->getError() == UPLOAD_ERR_OK;
    }

    function getContents()
    {
        return file_get_contents($this->getTmpName());
    }

    function destroy()
    {
        unlink($this->getTmpName());
    }
}
