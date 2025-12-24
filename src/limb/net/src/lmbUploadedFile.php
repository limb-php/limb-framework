<?php
/*
 * Limb PHP Framework
 *
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
 */
class lmbUploadedFile implements UploadedFileInterface
{
    public $name;
    public $error;
    public $type;
    public $size;
    public $tmp_name;

    protected $stream;

    function __construct($chunk)
    {
        $this->name = $chunk['name'];
        $this->error = $chunk['error'];
        $this->type = $chunk['type'];
        $this->size = $chunk['size'];
        $this->tmp_name = $chunk['tmp_name'];
    }

    function getName()
    {
        return $this->name;
    }

    function getType()
    {
        return $this->type;
    }

    function getTmpName()
    {
        return $this->tmp_name;
    }

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
