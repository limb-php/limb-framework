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
use Psr\Http\Message\UploadedFileInterface;

/**
 * class lmbUploadedFile.
 *
 * @package net
 * @version $Id$
 *
 * @method string getName()
 */
class lmbUploadedFile extends lmbObject implements UploadedFileInterface
{
    public $name;
    public $error;
    public $type;
    public $size;
    public $tmp_name;

    protected $stream;

    function getFilePath()
    {
        return $this->getTmpName();
    }

    /** @deprecated  */
    function getMimeType()
    {
        return $this->getClientMediaType();
    }

    /** @deprecated  */
    function move($dest)
    {
        return $this->moveTo($dest);
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

    public function getStream()
    {
        return $this->stream;
    }

    public function moveTo(string $targetPath)
    {
        return move_uploaded_file($this->getTmpName(), $targetPath);
    }

    public function getClientFilename()
    {
        return $this->name;
    }

    public function getClientMediaType()
    {
        return $this->getType();
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }
}
