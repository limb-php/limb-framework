<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\toolkit;

use limb\active_record\src\toolkit\lmbARTools;
use limb\config\src\toolkit\lmbConfTools;
use limb\i18n\src\toolkit\lmbI18NTools;
use limb\log\src\toolkit\lmbLogTools;
use limb\net\src\toolkit\lmbNetTools;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbEnv;
use limb\core\src\lmbString;
use limb\toolkit\src\lmbAbstractTools;
use limb\session\src\lmbSession;
use limb\view\src\lmbDummyView;
use limb\view\src\toolkit\lmbViewTools;
use limb\web_app\src\util\lmbFlashBox;
use limb\web_app\src\request\lmbRoutes;
use limb\core\src\exception\lmbException;
use limb\fs\src\exception\lmbFileNotFoundException;
use limb\web_app\src\exception\lmbControllerNotFoundException;

lmbEnv::setor('LIMB_ENABLE_MOD_REWRITE', true); // we assume mod_rewrite in ON by default

/**
 * class lmbWebAppTools.
 *
 * @package web_app
 * @version $Id: lmbWebAppTools.php 8176 2010-04-23 16:41:47Z
 */
class lmbWebAppTools extends lmbAbstractTools
{
  protected $session;
  protected $view;
  protected $dispatched_controller;
  protected $routes;
  protected $flash_box;

  static function getRequiredTools()
  {
    return [
      lmbI18NTools::class,
      lmbConfTools::class,
      lmbARTools::class,
      lmbNetTools::class,
      lmbViewTools::class,
      lmbLogTools::class
    ];
  }

  static function _init()
  {
    if(PHP_SAPI == 'cli')
    {
      lmbEnv::setor('LIMB_HTTP_GATEWAY_PATH', '/');
      lmbEnv::setor('LIMB_HTTP_BASE_PATH', '/');
      lmbEnv::setor('LIMB_HTTP_REQUEST_PATH', '/');
      lmbEnv::setor('LIMB_HTTP_SHARED_PATH', '/shared/');
      lmbEnv::setor('LIMB_HTTP_OFFSET_PATH', '');
    }
    else
    {
      $request = lmbToolkit::instance()->getRequest();

      if(!lmbEnv::has('LIMB_HTTP_OFFSET_PATH'))
      {
        $offset = trim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if($offset && $offset != '.')
          lmbEnv::setor('LIMB_HTTP_OFFSET_PATH', $offset . '/');
        else
          lmbEnv::setor('LIMB_HTTP_OFFSET_PATH', '');
      }

      if(substr(lmbEnv::get('LIMB_HTTP_OFFSET_PATH'), 0, 1) == '/')
        throw new lmbException('LIMB_HTTP_OFFSET_PATH constant must not have starting slash(' . lmbEnv::get('LIMB_HTTP_OFFSET_PATH') . ')!!!');

      lmbEnv::setor('LIMB_HTTP_REQUEST_PATH', $request->getUri()->toString());

      //HTTP_BASE_PATH is defined automatically according to current host and offset settings
      lmbEnv::setor('LIMB_HTTP_BASE_PATH', $request->getUri()->toString(
          array('protocol', 'user', 'password', 'host', 'port')) . '/' . lmbEnv::get('LIMB_HTTP_OFFSET_PATH'));

      if (substr(lmbEnv::get('LIMB_HTTP_BASE_PATH'), -1, 1) != '/') {
        echo('LIMB_HTTP_BASE_PATH constant must have trailing slash(' . lmbEnv::get('LIMB_HTTP_BASE_PATH') . ')!!!');
        exit(1);
      }

      if(!lmbEnv::has('LIMB_HTTP_GATEWAY_PATH'))
      {
        if(lmbEnv::has('LIMB_ENABLE_MOD_REWRITE'))
          lmbEnv::setor('LIMB_HTTP_GATEWAY_PATH', lmbEnv::get('LIMB_HTTP_BASE_PATH'));
        else
          lmbEnv::setor('LIMB_HTTP_GATEWAY_PATH', lmbEnv::get('LIMB_HTTP_BASE_PATH') . 'index.php/');
      }

      lmbEnv::setor('LIMB_HTTP_SHARED_PATH', lmbEnv::get('LIMB_HTTP_BASE_PATH') . 'shared/');

      if(substr(lmbEnv::get('LIMB_HTTP_SHARED_PATH'), -1, 1) != '/')
      {
        echo('LIMB_HTTP_SHARED_PATH constant must have trailing slash(' . lmbEnv::get('LIMB_HTTP_SHARED_PATH') . ')!!!');
        exit(1);
      }
    }
  }

  function getSession()
  {
    if(is_object($this->session))
      return $this->session;

    $this->session = new lmbSession();

    return $this->session;
  }

  function setSession($session)
  {
    $this->session = $session;
  }

  function setView($view)
  {
    $this->view = $view;
  }

  function getView()
  {
    if(is_object($this->view))
      return $this->view;

    //default view
    $this->view = new lmbDummyView('dummy_template_path');

    return $this->view;
  }

  function setDispatchedController($dispatched)
  {
    $this->dispatched_controller = $dispatched;
  }

  function getDispatchedController()
  {
    return $this->dispatched_controller;
  }

  function getRouteUrlByName($route_name, $params = array())
  {
      $routes = $this->toolkit->getRoutes();

      return lmbEnv::get('LIMB_HTTP_GATEWAY_PATH') . ltrim($routes->toUrl($params, $route_name), '/');
  }

  function getRoutesUrl($params = array(), $route_name = '', $skip_controller = false)
  {
    $routes = $this->toolkit->getRoutes();
    if(!isset($params['controller']) && !$skip_controller)
      $params['controller'] = $this->toolkit->getDispatchedController()->getName();

    return lmbEnv::get('LIMB_HTTP_GATEWAY_PATH') . ltrim($routes->toUrl($params, $route_name), '/');
  }

  function getRoutes()
  {
    if(!$this->routes)
    {
      $config = $this->toolkit->getConf('routes');

      $this->routes = new lmbRoutes($config->export());
    }

    return $this->routes;
  }

  function setRoutes($routes)
  {
    $this->routes = $routes;
  }

  function getFlashBox()
  {
    if(!is_object($this->flash_box))
    {
      $this->flash_box = lmbFlashBox::create($this->toolkit->getSession());
    }

    return $this->flash_box;
  }

  function flashError($message)
  {
    $this->toolkit->getFlashBox()->addError($message);
  }

  function flashMessage($message)
  {
    $this->toolkit->getFlashBox()->addMessage($message);
  }

  function createController($controller_name, $namespace = '')
  {
    if( strrpos($controller_name, 'Controller') == 0 )
      $controller_name .= 'Controller';

    if($namespace)
      $controller_name = $namespace . '\\' . lmbString::camel_case($controller_name);

    //$class = str_replace('\\', DIRECTORY_SEPARATOR, $controller_name);
    //$file = $class . '.php';
    //if( stream_resolve_include_path($file) === false )
    if ( !class_exists($controller_name) )
    {
      throw new lmbControllerNotFoundException("web_app: Could not find controller class '$controller_name'");
    }

    return new $controller_name;
  }

  function redirect($params_or_url = array(), $route_url = null, $append = '')
  {
    $toolkit = $this->toolkit;

    if(is_array($params_or_url))
      $toolkit->getResponse()->redirect($toolkit->getRoutesUrl($params_or_url, $route_url) . $append);
    else
      $toolkit->getResponse()->redirect($params_or_url . $append);
  }

  function isWebAppDebugEnabled()
  {
    $mode = false;
    if('devel' === lmbEnv::get('LIMB_APP_MODE'))
      $mode = true;

    try
    {
      $conf = $this->toolkit->getConf('common');

      if(isset($conf['debug_enabled']))
        $mode = $conf['debug_enabled'];
    }
    catch (lmbFileNotFoundException $e) {}

    return $mode;
  }

  function addVersionToUrl($file_src, $safe = false)
  {
    list($file_src, $version) = $this->toolkit->getNormalizeUrlAndVersion($file_src, $safe);
    //return '_/' . $version . '/' . ltrim($file_src, '/');
    return ltrim($file_src, '/') . '?' . $version;
  }

  function getNormalizeUrlAndVersion($file_src, $safe = false)
  {
    $doc_root = lmbEnv::get('LIMB_DOCUMENT_ROOT', false);
    if(!$doc_root)
      if($safe)
        return array($file_src, '00');
      else
        throw new lmbException('Not set require env LIMB_DOCUMENT_ROOT!');
    if(strpos($file_src, lmbEnv::get('LIMB_HTTP_BASE_PATH')) === 0)
      $file = substr($file_src, strlen(lmbEnv::get('LIMB_HTTP_BASE_PATH')), strlen($file_src));
    else
      $file = $file_src;

    $path = $doc_root . '/' . $file;
    if(is_file($path) && is_readable($path))
      $version = crc32(file_get_contents($path));
    else
      if($safe)
        $version = 0;
      else
        throw new lmbException('File \'' . $file_src . '\' not found in document root (' . $doc_root . ')');
    $version = ($version > 0 ? '1' : '0') . base_convert(abs($version), 10, 36);
    return array($file, $version);
  }

  function selectDomainForFile($domains, $file_src, $safe = false)
  {
    list($file_src, $version) = $this->toolkit->getNormalizeUrlAndVersion($file_src, $safe);
    return '//' . $domains[floor(fmod(abs(crc32($file_src)), count($domains)))] . '/_/' . $version . '/' . ltrim($file_src, '/');
  }
}
