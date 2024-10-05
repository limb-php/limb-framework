<?php

namespace limb\net;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class lmbPSR17Factory
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new lmbHttpRequest($uri, $method);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new lmbHttpResponse('', $code);
    }

    /** @TODO: fix lmbServerRequest */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new lmbServerRequest($uri, $method, $serverParams);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new lmbHttpStream( fopen($filename, $mode) );
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new lmbHttpStream($resource);
    }

    public function createUri(string $uri = '') : UriInterface
    {
        return new lmbUri($uri);
    }

    /** @TODO: fix lmbUploadedFile */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        return new lmbUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
}