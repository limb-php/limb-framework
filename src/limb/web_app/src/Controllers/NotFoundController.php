<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
        return response( view('not_found/display') )->setStatusCode(404, 'Not Found');
    }
}
