<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package net
 * @version $Id: common.inc.php 8041 2010-01-19 20:49:36Z
 */

use Limb\Net\lmbHttpResponse;
use Limb\Toolkit\lmbToolkit;
use limb\View\lmbViewInterface;
use Psr\Http\Message\ResponseInterface;

if (!function_exists('response')) {

    /**
     * @param string|lmbViewInterface $content
     */
    function response($content = '', $status = 200, $headers = []): ResponseInterface
    {
        if($content instanceof lmbViewInterface)
            $content = $content->render();

        return new lmbHttpResponse($content, $status, $headers);
    }

}

if (!function_exists('request')) {

    function request()
    {
        return lmbToolkit::instance()->getRequest();
    }

}

if (!function_exists( 'getallheaders' )) {

    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            } elseif (in_array($name, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

}
