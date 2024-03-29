<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\Controllers;

use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;
use limb\validation\src\lmbErrorList;
use limb\validation\src\lmbValidator;
use limb\core\src\lmbEnv;
use limb\core\src\lmbString;
use limb\web_app\src\Helpers\lmbRouteHelper;
use limb\view\src\lmbViewInterface;
use limb\core\src\exception\lmbException;
use limb\web_app\src\exception\lmbEmptyControllerResponseException;
use Psr\Http\Message\ResponseInterface;

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
     * @var string name of the controller
     */
    protected $name;

    /**
     * @var string default action that will be performed by performAction() if no current_action was specified
     * @deprecated
     */
    protected $default_action = 'display';

    /**
     * @var string
     */
    protected $current_action = 'display';

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
     * @var \limb\session\src\lmbSession
     */
    protected $session;
    /**
     *
     * @var lmbViewInterface
     */
    protected $_view;
    /**
     *
     * @var array
     */
    protected $error_list = [];
    /**
     *
     * @var lmbValidator
     */
    protected $validator;

    protected $form_id;
    protected $form_datasource = [];
    protected $_popup = true;
    protected $is_forwarded = false;

    function __construct()
    {
        if (!$this->name)
            $this->name = $this->_guessName();

        $this->toolkit = lmbToolkit::instance();

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

    /**
     *  Returns {@link $name}
     * @return string
     */
    function getName()
    {
        if ($this->name)
            return $this->name;

        return $this->name = $this->_guessName();
    }

    protected function _getTemplatePath($action)
    {
        return str_replace('.', DIRECTORY_SEPARATOR, $this->getName() . '.' . $action);
    }

    protected function _guessName(): string
    {
        return lmbRouteHelper::getControllerNameByClass($this);
    }

    function getView()
    {
        if ($this->_view)
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
        if (method_exists($this, $this->_mapActionToMethod($action)))
            return true;

        if ($this->findTemplateForAction($action))
            return true;

        return false;
    }

    function performAction($request)
    {
        if ($this->is_forwarded) {
            return false;
        }

        $template_path = $this->findTemplateForAction($this->getCurrentAction());
        if ($template_path) {
            $this->setTemplate($template_path); // Set View by default. Can be overridden in action method
        }

        if (method_exists($this, $method = $this->_mapCurrentActionToMethod())) {
            $controller_response = $this->{$method}($request);

            if ($controller_response !== null) {
                if (is_a($controller_response, ResponseInterface::class)) {
                    return $controller_response;
                } else {
                    $response = response();

                    if (is_a($controller_response, lmbViewInterface::class)) {
                        $response->getBody()->write( $controller_response->render() );

                        return $response;
                    } elseif (
                        $controller_response instanceof \ArrayObject ||
                        $controller_response instanceof \JsonSerializable ||
                        $controller_response instanceof \stdClass ||
                        is_array($controller_response)) {

                        $response->json( $controller_response );

                        return $response;
                    }

                    // string, _toString(), etc
                    $response->getBody()->write( $controller_response );

                    return $response;
                }
            }
        } elseif (!$template_path) {
            throw new lmbException('No method defined in controller "' .
                get_class($this) . '" for action "' . $this->getCurrentAction() . '" ' .
                'and no appropriate template found');
        }

        if ($view = lmbToolkit::instance()->getView()) {
            $this->_passLocalAttributesToView();

            $response = response();
            $response->getBody()->write($view->render());

            return $response;
        } else {
            throw new lmbEmptyControllerResponseException('Empty controller response');
        }
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
        if ($this->form_id && $this->error_list) {
            $this->getView()->setFormErrors($this->form_id, $this->error_list);
        }

        foreach ($this->form_datasource as $form_id => $datasource)
            $this->getView()->setFormDatasource($form_id, $datasource);

        foreach (get_object_vars($this) as $name => $value) {
            if ($name[0] == '_')
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
        if (!$form_id && !$this->form_id)
            throw new lmbException('There is no form id specified');

        if (!$form_id)
            $form_id = $this->form_id;

        $this->form_datasource[$form_id] = $datasource;
    }

    function redirect($params_or_url = array(), $route_url = null): ResponseInterface
    {
        return $this->toolkit->redirect($params_or_url, $route_url);
    }

    function flashError($message)
    {
        $this->toolkit->flashError($message);
    }

    function flashErrorAndRedirect($message, $redirect = array())
    {
        $this->flashError($message);
        return $this->redirect($redirect);
    }

    function flashMessage($message)
    {
        $this->toolkit->flashMessage($message);
    }

    function flash($message)
    {
        $this->flashMessage($message);
    }

    function flashAndRedirect($message, $redirect = array())
    {
        $this->flashMessage($message);
        return $this->redirect($redirect);
    }

    function addError($message, $fields = array(), $values = array())
    {
        $this->error_list->addError($message, $fields, $values);

        return $this;
    }

    function closePopup()
    {
        return response('<html><script>if(window.opener){window.opener.focus();window.opener.location.reload();window.close();}</script></html>');
    }

    protected function _mapCurrentActionToMethod()
    {
        return $this->_mapActionToMethod($this->getCurrentAction());
    }

    protected function _mapActionToMethod($action)
    {
        return lmbString::camel_case('do_' . $action, false);
    }

    function forward($controller_name, $action)
    {
        $this->is_forwarded = true;
        $controller = $this->toolkit->createController($controller_name);
        $controller->setCurrentAction($action);
        return $controller->performAction( request() );
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
        return (bool)lmbEnv::get('LIMB_CONTROLLER_CACHE_ENABLED');
    }

    function _loadCache()
    {
        if ($this->isCacheEnabled() && file_exists($cache = lmbEnv::get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache'))
            $this->action_template_map = unserialize(file_get_contents($cache));
    }

    function _saveCache()
    {
        if ($this->isCacheEnabled() && $this->map_changed) {
            lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache',
                serialize($this->action_template_map));
        }
    }

    function findTemplateForAction($action)
    {
        $controller_name = get_class($this);

        if (isset($this->action_template_map[$controller_name][$action]))
            return $this->action_template_map[$controller_name][$action];

        $template_format = $this->_getTemplatePath($action);

        $template_path = $this->findTemplateByAlias($template_format);
        $this->action_template_map[$controller_name][$action] = $template_path;
        $this->map_changed = true;

        return $template_path;
    }

    function findTemplateByAlias($template_format)
    {
        foreach ($this->toolkit->getSupportedViewTypes() as $ext => $view_class) {
            if ($template_path = $this->toolkit->locateTemplateByAlias($template_format . $ext, $view_class)) {
                return $template_path;
            }
        }
        return false;
    }

    protected function isAjaxRequest()
    {
        if ($this->request->hasHeader('DNT'))
            return true;

        return $this->request->hasHeader('X-Requested-With') &&
            $this->request->getHeaderLine('X-Requested-With') == 'XMLHttpRequest';
    }
}
