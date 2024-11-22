<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

/**
 * class lmbMetaRedirectStrategy.
 *
 * @package net
 * @version $Id: lmbMetaRedirectStrategy.php 7486 2009-01-26 19:13:20Z
 */
class lmbMetaRedirectStrategy
{
    protected $template_path;

    function __construct($template_path = null)
    {
        $this->template_path = $template_path;
    }

    function redirect($response, $path)
    {
        $response->write($this->_prepareDefaultResponse('Redirecting...', $path));
    }

    protected function _prepareDefaultResponse($message, $path)
    {
        return "<html><head><meta http-equiv=refresh content='0;url={$path}'></head>
            <body bgcolor=white>{$message}</body></html>";
    }
}
