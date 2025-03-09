<?php

namespace Limb\Net;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class lmbServerRequest extends lmbHttpRequest implements ServerRequestInterface
{
    protected $__serverParams = [];

    /**
     * @param string                               $method       HTTP method
     * @param string|UriInterface                  $uri          URI
     * @param (string|string[])[]                  $headers      Request headers
     * @param array                                $serverParams Typically the $_SERVER superglobal
     */
    public function __construct(
        string $method,
               $uri,
        array $headers = [],
        array $serverParams = []
    ) {
        $this->__serverParams = $serverParams;

        parent::__construct($uri, $method, [], [], [], [], $headers);
    }

    public function getServerParams()
    {
        return $this->__serverParams;
    }

    public function getCookieParams()
    {
        return $this->__cookies;
    }

    public function withCookieParams(array $cookies)
    {
        if ($this->__cookies === $cookies) {
            return $this;
        }

        $new = clone($this);
        $new->__cookies = $this->_stripHttpSlashes($cookies);
        return $new;
    }

    public function getUploadedFiles()
    {
        return $this->__files;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        if ($this->__files === $uploadedFiles) {
            return $this;
        }

        $new = clone($this);
        $new->__files = $this->_parseUploadedFiles($uploadedFiles);
        return $new;
    }

    public function withQueryParams(array $query)
    {
        if ($this->__get === $query) {
            return $this;
        }

        $new = clone($this);
        $new->__get = $this->_stripHttpSlashes($query);
        return $new;
    }

    public function getParsedBody()
    {
        return $this->__post;
    }

    public function withParsedBody($data)
    {
        if ($this->__post === $data) {
            return $this;
        }

        $new = clone($this);
        $new->__post = $this->_stripHttpSlashes($data);
        return $new;
    }

    public function getQueryParams()
    {
        return $this->__get;
    }
}
