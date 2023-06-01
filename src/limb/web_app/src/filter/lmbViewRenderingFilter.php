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
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbViewRenderingFilter.
 *
 * @package web_app
 * @version $Id: lmbViewRenderingFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbViewRenderingFilter implements lmbInterceptingFilterInterface
{
  function run($filter_chain, $request = null, $callback = null)
  {
      $response = $filter_chain->next($request, $callback);

      $toolkit = lmbToolkit::instance();

      if($response->isEmpty() && is_object($view = $toolkit->getView())) {
          //$view->set('request', $toolkit->getRequest());
          //$view->set('session', $toolkit->getSession());
          //$view->set('controller', $toolkit->getDispatchedController());
          //$view->set('toolkit', $toolkit);

          $response->write($view->render());
      }

      return $response;
  }
}
