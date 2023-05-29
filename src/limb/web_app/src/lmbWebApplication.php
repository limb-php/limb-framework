<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src;

use limb\filter_chain\src\lmbFilterChain;
use limb\core\src\lmbHandle;
use limb\web_app\src\Controllers\NotFoundController;
use limb\web_app\src\exception\lmbErrorHandler;
use limb\web_app\src\request\lmbRoutesRequestDispatcher;
use limb\web_app\src\filter\lmbSessionStartupFilter;
use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use limb\web_app\src\filter\lmbActionPerformingAndViewRenderingFilter;

/**
 * class lmbWebApplication.
 *
 * @package web_app
 * @version $Id: lmbWebApplication.php 7681 2009-03-04 05:58:40Z
 */
class lmbWebApplication extends lmbFilterChain
{
  protected $default_controller_name = NotFoundController::class;
  protected $pre_dispatch_filters = array();
  protected $pre_action_filters = array();
  protected $request_dispatcher = null;

  function setDefaultControllerName($name)
  {
    $this->default_controller_name = $name;
  }

  function setRequestDispatcher($dispatcher)
  {
    $this->request_dispatcher = $dispatcher;
  }

  protected function _getRequestDispatcher()
  {
    if(!is_object($this->request_dispatcher))
      return new lmbHandle(lmbRoutesRequestDispatcher::class);
    return $this->request_dispatcher;
  }

  function addPreDispatchFilter($filter)
  {
    $this->pre_dispatch_filters[] = $filter;
  }

  function addPreActionFilter($filter)
  {
    $this->pre_action_filters[] = $filter;
  }

  function process($request = null, $response = null): \limb\net\src\lmbHttpResponse
  {
      $this->_bootstrap();

      $this->_registerFilters();

      return parent::process($request, $response);
  }

  protected function _bootstrap()
  {
    (new lmbErrorHandler(dirname(__FILE__) . '/../template/server_error.html'))->bootstrap();
  }

  protected function _registerFilters()
  {
    $this->registerFilter(new lmbHandle(lmbSessionStartupFilter::class));

    $this->_addFilters($this->pre_dispatch_filters);

    $this->registerFilter(new lmbHandle(
        lmbRequestDispatchingFilter::class,
              array(
                  $this->_getRequestDispatcher(),
                  $this->default_controller_name
              )
        )
    );

    $this->_addFilters($this->pre_action_filters);

    $this->registerFilter(new lmbHandle(lmbActionPerformingAndViewRenderingFilter::class));
  }

  protected function _addFilters($filters)
  {
    foreach($filters as $filter)
      $this->registerFilter($filter);
  }
}
