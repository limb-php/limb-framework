<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\Controllers;

/**
 * class ServerErrorController.
 *
 * @package web_app
 * @version $Id$
 */
class ServerErrorController extends LmbController
{
    function doDisplay()
    {
        $this->setTemplate('server_error.html');
        return response()->addHeader('HTTP/1.x 500 Server Error');
    }
}
