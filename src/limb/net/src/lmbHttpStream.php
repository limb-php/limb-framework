<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

use Psr\Http\Message\StreamInterface;

/**
 * class lmbHttpStream.
 *
 * @package net
 * @version $Id$
 */
class lmbHttpStream implements StreamInterface
{
    private const READ_WRITE_MODE = [
        'read' => '/r|a\+|ab\+|w\+|wb\+|w\+b|x\+|xb\+|c\+|cb\+/',
        'write' => '/a|w|r\+|rb\+|rw|x|c/'
    ];

    /** @var $stream resource|null */
    private $stream;
    private $size;
    private $seekable;
    private $writable;
    private $readable;

    public function __construct($body = '')
    {
        if (!(is_string($body) || is_resource($body))) {
            throw new \InvalidArgumentException('Argument 1 MUST be a String, Resource or null, ' . get_debug_type($body) . ' given');
        }

        if (is_string($body)) {
            $resource = fopen('php://temp', 'w+');
            fwrite($resource, $body);
            $body = $resource;
        }
        $this->stream = $body;
        $this->seekable = null;
        $this->writable = null;
        $this->readable = null;

        if ($this->isSeekable()) {
            fseek($body, 0);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (\Throwable $exception) {
            return '';
        }
    }

    public function close(): void
    {
        if (is_resource($this->stream)) {
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
        if ($this->size !== null) {
            return $this->size;
        }

        if ($this->stream === null) {
            return null;
        }

        $stat = fstat($this->stream);

        return $this->size = $stat['size'] ?? null;
    }

    public function tell(): int
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Unable to get current position');
        }

        $position = ftell($this->stream);
        if ($position === false) {
            throw new \RuntimeException('Unable to get current position');
        }

        return $position;
    }

    public function eof(): bool
    {
        return ($this->stream !== null) && feof($this->stream);
    }

    public function isSeekable(): bool
    {
        if ($this->seekable === null) {
            $this->seekable = $this->getMetadata('seekable') ?? false;
        }

        return $this->seekable;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('Unable to seek stream position');
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        if (!is_resource($this->stream)) {
            return false;
        }

        if ($this->writable === null) {
            $mode = $this->getMetadata('mode');
            //$this->writable = in_array($mode, self::READ_WRITE_MODE['write']);

            $this->writable = (bool)preg_match(self::READ_WRITE_MODE['write'], $mode);
        }

        return $this->writable;
    }

    public function write($string): int
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Stream is not writable');
        }

        $result = fwrite($this->stream, $string);
        if ($result === false) {
            throw new \RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    public function isReadable(): bool
    {
        if (!is_resource($this->stream)) {
            return false;
        }

        if ($this->readable === null) {
            $mode = $this->getMetadata('mode');
            //$this->readable = in_array($mode, self::READ_WRITE_MODE['read']);

            $this->readable = (bool)preg_match(self::READ_WRITE_MODE['read'], $mode);
        }

        return $this->readable;
    }

    public function read($length): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable');
        }

        $result = fread($this->stream, $length);
        if ($result === false) {
            throw new \RuntimeException('Unable to read from stream');
        }

        return $result;
    }

    public function getContents(): string
    {
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        $contents = stream_get_contents($this->stream);
        if ($contents === false) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    public function getMetadata($key = null)
    {
        if ($this->stream === null) {
            return $key === null ? null : false;
        }

        $meta = stream_get_meta_data($this->stream);
        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
