<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * class lmbHttpMessage.
 *
 * @package net
 * @version $Id$
 */
abstract class lmbHttpMessage implements MessageInterface
{
    protected $protocol = '1.1';
    protected $headers = [];
    protected $body;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version): self
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $clone = clone $this;
        $clone->protocol = $version;

        return $clone;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        $name = strtolower($name);

        return isset($this->headers[$name]);
    }

    public function getHeader($name): array
    {
        $name = strtolower($name);
        if (!$this->hasHeader($name)) {
            return [];
        }

        return $this->headers[$name];
    }

    public function getHeaderLine($name): string
    {
        $values = $this->getHeader($name);

        return implode(',', $values);
    }

    public function withHeader($name, $value): self
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Argument 1 must be a string');
        }

        if (!is_string($name) && !is_array($value)) {
            throw new \InvalidArgumentException('Argument 2 must be a string');
        }

        $name = strtolower($name);
        if (is_string($value)) {
            $value = [$value];
        }

        $clone = clone $this;
        $clone->headers[$name] = $value;

        return $clone;
    }

    public function withAddedHeader($name, $value): self
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Argument 1 must be a string');
        }

        if (!is_string($name) && !is_array($value)) {
            throw new \InvalidArgumentException('Argument 2 must be a string');
        }

        $name = strtolower($name);
        if (is_string($value)) {
            $value = [$value];
        }

        $clone = clone $this;
        $clone->headers[$name] = array_merge($clone->headers, $value);

        return $clone;
    }

    public function withoutHeader($name): self
    {
        $clone = clone $this;
        unset($clone->headers[$name]);

        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): self
    {
        $clone = clone($this);
        $clone->body = $body;

        return $clone;
    }

    protected function setHeaders($headers): void
    {
        foreach ($headers as $headerName => $value) {
            $normalizedHeaderName = strtolower($headerName);
            $this->headers[$normalizedHeaderName] = $value;
            if (is_string($value)) {
                $this->headers[$normalizedHeaderName] = explode(',', $value);
            }
        }
    }

    protected function setBody($body): void
    {
        if (!($body instanceof StreamInterface)) {
            $body = new lmbHttpStream($body);
        }

        $this->body = $body;
    }

    protected function inHeader(string $name, string $value)
    {
        $headerValues = $this->getHeader($name);

        return in_array($value, $headerValues);
    }
}
