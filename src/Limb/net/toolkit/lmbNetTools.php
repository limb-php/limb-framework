<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\toolkit;

use limb\toolkit\lmbAbstractTools;
use limb\net\lmbHttpRequest;
use limb\net\lmbHttpResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * class lmbNetTools.
 *
 * @package net
 * @version $Id: lmbNetTools.php 7486 2009-01-26 19:13:20Z
 */
class lmbNetTools extends lmbAbstractTools
{
    protected $response;
    protected $request;

    function getRequest(): RequestInterface
    {
        if (is_object($this->request))
            return $this->request;

        $this->request = lmbHttpRequest::createFromGlobals();

        return $this->request;
    }

    function setRequest($request): void
    {
        $this->request = $request;
    }

    /** @deprecated  */
    function getResponse($content = '', $status = 200, $headers = []): ResponseInterface
    {
        $this->response = new lmbHttpResponse($content, $status, $headers);

        return $this->response;
    }

    /** @deprecated  */
    function setResponse($response): void
    {
        $this->response = $response;
    }
}
