<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\search\src\controller;

use limb\web_app\src\controller\lmbController;

/**
 * class SearchController.
 *
 * @package search
 * @version $Id: SearchController.php 7686 2009-03-04 19:57:12Z
 */
class SearchController extends lmbController
{
    function doDisplay()
    {
        $this->useForm('search_form');
    }
}
