<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\Net\filter;

use limb\cms\src\Net\request\lmbCmsDocumentRequestDispatcher;
use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use limb\web_app\src\request\lmbCompositeRequestDispatcher;
use limb\web_app\src\request\lmbRoutesRequestDispatcher;

/**
 * class lmbCmsRequestDispatchingFilter.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsRequestDispatchingFilter extends lmbRequestDispatchingFilter
{
    function __construct($default_controller_name = NotFoundController::class)
    {
        $dispatcher = new lmbCompositeRequestDispatcher();
        $dispatcher->addDispatcher(new lmbCmsDocumentRequestDispatcher());
        $dispatcher->addDispatcher(new lmbRoutesRequestDispatcher());

        parent::__construct($dispatcher, $default_controller_name);
    }
}
