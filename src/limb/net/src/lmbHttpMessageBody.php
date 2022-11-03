<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\net\src;

use http\Exception\RuntimeException;
use Psr\Http\Message\StreamInterface;

/**
 * class lmbHttpMessageBody.
 *
 * @package net
 * @version $Id$
 */
class lmbHttpMessageBody implements StreamInterface
{
    /** @var $stream resource|null */
    private $stream;
    private $size;
    private $seekable;

    public function __construct($body = null)
    {
        if( !is_string($body) && !is_resource($body) && $body === null ) {
            throw new \InvalidArgumentException('Argument 1 MUST be a String, Resource or null');
        }

        if(is_string($body)) {
            $resource = fopen('php://temp', 'w+');
            fwrite($resource, $body);
            $body = $resource;
        }

        $this->stream = $body;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    public function close(): void
    {
        if(is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->detach();
    }

    public function detach()
    {
        $resource = $this->stream;
        unset($this->stream);

        return $resource;
    }

    public function getSize(): ?int
    {
        if($this->size !== null) {
            return $this->size;
        }

        if($this->stream === null) {
            return null;
        }

        $stat = fstat($this->stream);

        return $this->size = $stat['size'] ?? null;
    }

    public function tell()
    {
        if($this->stream === null) {
            throw new RuntimeException('Unable to get current position');
        }

        $position = ftell($this->stream);
        if($position === false) {
            throw new RuntimeException('Unable to get current position');
        }

        return $position;
    }

    public function eof(): bool
    {
        return ($this->stream !== null) && feof($this->stream);
    }

    public function isSeekable()
    {
        // TODO: Implement isSeekable() method.
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        // TODO: Implement seek() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    public function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    public function write($data)
    {
        $this->body .= $data;
    }

    public function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    public function read($length)
    {
        // TODO: Implement read() method.
    }

    public function getContents()
    {
        return $this->body;
    }

    public function getMetadata($key = null)
    {
        // TODO: Implement getMetadata() method.
    }
}
