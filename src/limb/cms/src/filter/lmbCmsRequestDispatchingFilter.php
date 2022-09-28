<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\cms\src\filter;

use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use limb\web_app\src\request\lmbCompositeRequestDispatcher;
use limb\cms\src\request\lmbCmsDocumentRequestDispatcher;
use limb\web_app\src\request\lmbRoutesRequestDispatcher;

/**
 * class lmbCmsRequestDispatchingFilter.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsRequestDispatchingFilter extends lmbRequestDispatchingFilter
{
  function __construct($default_controller_name = 'NotFoundController')
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    
    $dispatcher->addDispatcher(new lmbCmsDocumentRequestDispatcher());
    $dispatcher->addDispatcher(new lmbRoutesRequestDispatcher());
    
    parent::__construct($dispatcher, $default_controller_name);
  }
}
