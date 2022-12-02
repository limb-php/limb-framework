<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\controller;

use limb\web_app\src\controller\LmbController;

/**
 * class FallbackToViewController.
 * To use this controller just pass it's name to lmbRequestDispatchingFilter, e.g.:
 *  <code>
 *   $this->registerFilter(new lmbHandle('limb\web_app\src\filter\lmbRequestDispatchingFilter',
 *                                       array(new lmbHandle('limb\web_app\src\request\lmbRoutesRequestDispatcher'),
 *                                            'any_template')));
 *  </code>
 *
 * @package web_app
 * @version $Id: lmbController.php 6497 2007-11-07 13:27:32Z
 */
class FallbackToViewController extends LmbController
{
  function performAction($request = null)
  {
    $path = trim($this->request->getUriPath(), '/');
    if($template_path = $this->findTemplateByAlias($path))
    {
      $view = $this->toolkit->createViewByTemplate($template_path);
      $this->toolkit->setView($view);
    }
    else
      return $this->forwardTo404();
  }

  function actionExists($action)
  {
    return true;
  }
}
