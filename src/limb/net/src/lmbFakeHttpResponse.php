<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

/**
 * class lmbFakeHttpResponse.
 *
 * @package net
 * @version $Id$
 */
class lmbFakeHttpResponse extends lmbHttpResponse
{
    protected function _sendHeader($header, $value)
    {
    }

    protected function _sendCookie($cookie)
    {
    }

    protected function _sendFile($file_path)
    {
    }
}
