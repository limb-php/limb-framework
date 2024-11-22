<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

/**
 * class lmbHttpRedirectStrategy.
 *
 * @package net
 * @version $Id: lmbHttpRedirectStrategy.php 7486 2009-01-26 19:13:20Z
 */
class lmbHttpRedirectStrategy
{
    function redirect($response, $path)
    {
        $response->addHeader("Location", $path);
    }
}
