<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\Controllers;

/**
 * class NotFoundController.
 *
 * @package web_app
 * @version $Id: NotFoundController.php 7486 2009-01-26 19:13:20Z
 */
class NotFoundController extends LmbController
{
    function doDisplay($request)
    {
        return response( view('not_found/display') )
            ->setStatusCode(404, 'Not Found');
    }
}
