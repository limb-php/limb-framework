<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace limb\cms\src\filter;

use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\Filter\lmbRequestDispatchingFilter;
use limb\web_app\src\Request\lmbCompositeRequestDispatcher;
use limb\Cms\src\Request\lmbCmsDocumentRequestDispatcher;
use limb\web_app\src\Request\lmbRoutesRequestDispatcher;

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
