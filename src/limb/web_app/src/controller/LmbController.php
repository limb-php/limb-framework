<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\controller;

use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbMixable;
use limb\fs\src\lmbFs;
use limb\validation\src\lmbErrorList;
use limb\validation\src\lmbValidator;
use limb\core\src\lmbEnv;
use limb\core\src\lmbString;
use limb\core\src\exception\lmbException;
use limb\view\src\lmbView;

lmbEnv::setor('LIMB_CONTROLLER_CACHE_ENABLED', true);

/**
 * class lmbController.
 *
 * @package web_app
 * @version $Id: lmbController.php 8117 2010-01-31 11:20:22Z
 */
class LmbController
{
  /**
   * @var array array of mixins
   */
  protected $mixins = array();

  /**
   * @var object lmbMixable instance
   */
  protected $mixed;

  /**
   * @var string name of the controller
   */
  protected $name;

  /**
   * @var string default action that will be performed by performAction() if no current_action was specified
   */
  protected $default_action = 'display';

  /**
   * @var string
   */
  protected $current_action;

  /**
   * @var array a pairs of action to template cached map
   */
  protected $action_template_map = array();

  /**
   * @var boolean
   */
  protected $map_changed = false;

  /**
   * @var object lmbToolkit instance
   */
  protected $toolkit;
  /**
   *
   * @var \limb\net\src\lmbHttpRequest
   */
  protected $request;

  /**
   *
   * @var \limb\net\src\lmbHttpResponse
   */
  protected $response;
  /**
   *
   * @var \limb\session\src\lmbSession
   */
  protected $session;
  /**
   *
   * @var lmbView
   */
  protected $_view;
  /**
   *
   * @var array
   */
  protected $error_list;
  /**
   *
   * @var lmbValidator
   */
  protected $validator;

  protected $form_id;
  protected $form_datasource = [];
  protected $in_popup = true;
  protected $is_forwarded = false;

  function __construct()
  {
    $this->mixed = new lmbMixable();
    $this->mixed->setOwner($this);
    foreach($this->mixins as $mixin)
      $this->mixed->mixin($mixin);

    if(!$this->name)
     $this->name = $this->_guessName();

    $this->toolkit = lmbToolkit::instance();

    $this->request = $this->toolkit->getRequest();
    $this->response = $this->toolkit->getResponse();
    $this->session = $this->toolkit->getSession();
    $this->error_list = new lmbErrorList();
    $this->validator = new lmbValidator($this->error_list);

    $this->_loadCache();
  }

  function getDefaultAction()
  {
    return $this->default_action;
  }

  function setCurrentAction($action)
  {
    $this->current_action = $action;
  }

  function getCurrentAction()
  {
    return $this->current_action;
  }

  protected function _guessName()
  {
    $refController = new \ReflectionClass($this);
    $ctrlClassName = $refController->getShortName();

    $pos = strpos($ctrlClassName, 'Controller');
    return lmbString::under_scores(substr($ctrlClassName, 0, $pos));
  }

  /**
   *  Returns {@link $name}
   *  @return string
   */
  function getName()
  {
    return $this->name;
  }

  function getView()
  {
      if($this->_view)
          return $this->_view;

      return $this->_view = $this->toolkit->getView();
  }

  function validate($dataspace)
  {
    $this->validator->validate($dataspace);
    return $this->validator->isValid();
  }

  function actionExists($action)
  {
    if(method_exists($this, $this->_mapActionToMethod($action)))
      return true;

    if($this->findTemplateForAction($action))
      return true;

    return false;
  }

  function performAction($request)
  {
      if($this->is_forwarded) {
          return false;
      }

      $template_path = $this->findTemplateForAction($this->current_action);
      if($template_path)
          $this->setTemplate($template_path); // Set View by default. Can be overrided in action method

      $result = null;

      if(method_exists($this, $method = $this->_mapCurrentActionToMethod())) {
          $result = $this->$method($request);
      }
      elseif(!$template_path) {
          throw new lmbException('No method defined in controller "' .
              get_class($this) . '" for action "' . $this->current_action . '" ' .
              'and no appropriate template found');
      }

      if(is_a($result, lmbView::class)) {
          $this->toolkit->setView($result);
      }
      else {
          $this->_passLocalAttributesToView();
      }

      return $result;
  }

  function useForm($form_id, $datasource = null)
  {
    $this->form_id = $form_id;

    if ($datasource)
      $this->setFormDatasource($datasource);
  }

  function setTemplate($template_path)
  {
      $this->_view = $this->toolkit->createViewByTemplate($template_path);

      $this->toolkit->setView($this->_view);
  }

  protected function _passLocalAttributesToView()
  {
      if($this->form_id && $this->error_list) {
          $this->getView()->setFormErrors($this->form_id, $this->error_list);
      }

      foreach($this->form_datasource as $form_id => $datasource)
          $this->getView()->setFormDatasource($form_id, $datasource);

      foreach(get_object_vars($this) as $name => $value)
      {
          if($name[0] == '_')
            continue;
          $this->getView()->set($name, $value);
      }
  }

  function passToView($var, $value)
  {
    $this->getView()->set($var, $value);
  }

  function resetView()
  {
    $this->getView()->reset();
  }

  function setFormDatasource($datasource, $form_id = null)
  {
    if(!$form_id && !$this->form_id)
      throw new lmbException('There is no form id specified');

    if(!$form_id)
      $form_id = $this->form_id;

    $this->form_datasource[$form_id] = $datasource;
  }

  function redirect($params_or_url = array(), $route_url = null)
  {
    $this->toolkit->redirect($params_or_url, $route_url);
  }

  function flashError($message)
  {
    $this->toolkit->flashError($message);
  }

  function flashErrorAndRedirect($message, $redirect = array())
  {
    $this->flashError($message);
    $this->redirect($redirect);
  }

  function flashMessage($message)
  {
    $this->toolkit->flashMessage($message);
  }

  function flashAndRedirect($message, $redirect = array())
  {
    $this->flashMessage($message);
    $this->redirect($redirect);
  }

  function flash($message)
  {
    $this->flashMessage($message);
  }

  function addError($message, $fields = array(), $values = array())
  {
    $this->error_list->addError($message, $fields, $values);
  }

  function closePopup()
  {
    if(!$this->in_popup)
      return;

    $this->response->write('<html><script>if(window.opener){window.opener.focus();window.opener.location.reload();window.close();}</script></html>');
  }

  protected function _mapCurrentActionToMethod()
  {
    return $this->_mapActionToMethod($this->current_action);
  }

  protected function _mapActionToMethod($action)
  {
    return lmbString::camel_case('do_' . $action);
  }

  function forward($controller_name, $action)
  {
    $this->is_forwarded = true;
    $controller = $this->toolkit->createController($controller_name);
    $controller->setCurrentAction($action);
    return $controller->performAction( $this->toolkit->getRequest() );
  }

  function forwardTo404()
  {
    return $this->forward(NotFoundController::class, 'display');
  }

  function forwardTo500()
  {
    return $this->forward(ServerErrorController::class, 'display');
  }


  function __destruct()
  {
    $this->_saveCache();
  }

  function isCacheEnabled()
  {
    return (bool) lmbEnv::get('LIMB_CONTROLLER_CACHE_ENABLED');
  }

  function _loadCache()
  {
    if($this->isCacheEnabled() && file_exists($cache = lmbEnv::get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache'))
      $this->action_template_map = unserialize(file_get_contents($cache));
  }

  function _saveCache()
  {
    if($this->map_changed && $this->isCacheEnabled())
    {
      lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache',
                       serialize($this->action_template_map));
    }
  }

  /**
   * Using this hacky method mixins can access controller variables
   * @param string variable name
   * @return mixed
   */
  function _get($name)
  {
    if(isset($this->$name))
      return $this->$name;
  }

  function findTemplateForAction($action)
  {
      $controller_name = get_class($this);

      if( isset($this->action_template_map[$controller_name]) && isset($this->action_template_map[$controller_name][$action]) )
          return $this->action_template_map[$controller_name][$action];

      $template_format = $this->getName() . '/' . $action;

      $template_path = $this->findTemplateByAlias($template_format);
      $this->action_template_map[$controller_name][$action] = $template_path;
      $this->map_changed = true;

      return $template_path;
  }

  function findTemplateByAlias($template_format)
  {
    foreach($this->toolkit->getSupportedViewExtensions() as $ext)
    {
      if($template_path = $this->toolkit->locateTemplateByAlias($template_format . $ext))
      {
        return $template_path;
      }
    }
    return false;
  }
}
