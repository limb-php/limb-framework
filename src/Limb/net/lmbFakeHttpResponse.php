<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net;

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
