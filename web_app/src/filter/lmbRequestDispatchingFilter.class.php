<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\web_app\src\request\lmbRequestDispatcherInterface;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;
use limb\web_app\src\exception\lmbControllerNotFoundException;

/**
 * class lmbRequestDispatchingFilter.
 *
 * @package web_app
 * @version $Id: lmbRequestDispatchingFilter.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbRequestDispatchingFilter implements lmbInterceptingFilterInterface
{
  protected $toolkit;
  protected $dispatcher;
  protected $default_controller_name;

  function __construct($dispatcher, $default_controller_name = 'NotFoundController')
  {
    $this->toolkit = lmbToolkit :: instance();
    $this->dispatcher = $dispatcher;
    $this->setDefaultControllerName($default_controller_name);
  }

  function setDefaultControllerName($default_controller_name)
  {
    $this->default_controller_name = $default_controller_name;
  }

  function run($filter_chain)
  {
    $dispatched_params = $this->dispatcher->dispatch($this->toolkit->getRequest());

    $this->_putOtherParamsToRequest($dispatched_params);

    $controller = $this->_createController($dispatched_params);

    if(isset($dispatched_params['action']) && $controller->actionExists($dispatched_params['action']))
      $controller->setCurrentAction($dispatched_params['action']);
    elseif(!isset($dispatched_params['action']))
      $controller->setCurrentAction($controller->getDefaultAction());
    else
      $controller = $this->_createDefaultController();

    $this->toolkit->setDispatchedController($controller);

    $filter_chain->next();
  }

  protected function _createController($dispatched_params)
  {
    if(!isset($dispatched_params['controller']))
      $dispatched_params['controller'] = $this->default_controller_name;

    try
    {
      $controller = $this->toolkit->createController($dispatched_params['controller'], $dispatched_params['namespace'] ?? '');
    }
    catch(lmbControllerNotFoundException $e)
    {
      $controller = $this->toolkit->createController($this->default_controller_name, $this->default_controller_namespace);
    }

    return $controller;
  }

  protected function _createDefaultController()
  {
    $controller = $this->toolkit->createController($this->default_controller_name, $this->default_controller_namespace);
    $controller->setCurrentAction($controller->getDefaultAction());

    return $controller;
  }

  protected function _putOtherParamsToRequest($dispatched_params)
  {
    $this->toolkit->getRequest()->merge($dispatched_params);
  }
}


