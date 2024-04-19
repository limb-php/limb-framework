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
use Psr\Http\Message\RequestInterface;
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
    static protected $action_template_map = [];
    /**
     * @var boolean
     */
    static protected $map_loaded = false;

    /**
     * @var object lmbToolkit instance
     */
    protected $toolkit;

    /**
     *
     * @var lmbErrorList
     */
    protected $error_list;
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

        $this->error_list = new lmbErrorList();
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
     * Returns {@link $name}
     * @return string
     */
    function getName(): string
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

    /** @return lmbViewInterface|null */
    function getView()
    {
        $view = $this->toolkit->getView();
        if($view)
            $this->_passLocalAttributesToView($view);

        return $view;
    }

    function validateRequest($dataspace): bool
    {
        return $this->getValidator()->validate($dataspace);
    }

    function getErrorList()
    {
        return $this->error_list;
    }

    function getValidator()
    {
        if(!$this->validator)
            $this->validator = new lmbValidator($this->error_list);

        return $this->validator;
    }

    function actionExists($action): bool
    {
        if (method_exists($this, $this->_mapActionToMethod($action)))
            return true;

        if ($this->findTemplateForAction($action))
            return true;

        return false;
    }

    function performCommand($class_name, ...$args)
    {
        $command = new $class_name();
        $command->runCommand($args);
    }

    /**
     * @return ResponseInterface|false
     * @throws lmbException
     * @throws lmbEmptyControllerResponseException
     */
    function performAction(RequestInterface $request)
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
                        $this->toolkit->setView($controller_response);
                        //$this->_passLocalAttributesToView();

                        $response->getBody()->write($this->getView()->render());

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

        if( $view = $this->getView() ) {
            //$this->_passLocalAttributesToView();

            $response = response();
            $response->getBody()->write($view->render());

            return $response;
        }

        throw new lmbEmptyControllerResponseException('Empty controller response');
    }

    function useForm($form_id, $datasource = null)
    {
        $this->form_id = $form_id;

        if ($datasource)
            $this->setFormDatasource($datasource);
    }

    function setFormDatasource($datasource, $form_id = null): void
    {
        if ($form_id !== null)
            $this->form_id  = $form_id;

        if (!$this->form_id)
            throw new lmbException('There is no form id specified');

        $this->form_datasource[$this->form_id] = $datasource;
    }

    function setTemplate($template_path)
    {
        $this->toolkit->setView( $this->toolkit->createViewByTemplate($template_path) );
    }

    protected function _passLocalAttributesToView($view): void
    {
        if ($this->form_id && $this->error_list)
            $view->setFormErrors($this->form_id, $this->error_list);

        foreach ($this->form_datasource as $form_id => $datasource)
            $view->setFormDatasource($form_id, $datasource);

        foreach (get_object_vars($this) as $name => $value) {
            if ($name[0] !== '_')
                $view->set($name, $value);
        }
    }

    function redirect($params_or_url = array(), $route_url = null): ResponseInterface
    {
        return $this->toolkit->redirect($params_or_url, $route_url);
    }

    function flashError($message): void
    {
        $this->toolkit->flashError($message);
    }

    function flashErrorAndRedirect($message, $redirect = array()): ResponseInterface
    {
        $this->flashError($message);
        return $this->redirect($redirect);
    }

    function flashMessage($message): void
    {
        $this->toolkit->flashMessage($message);
    }

    function flash($message): void
    {
        $this->flashMessage($message);
    }

    function flashAndRedirect($message, $redirect = array()): ResponseInterface
    {
        $this->flashMessage($message);
        return $this->redirect($redirect);
    }

    function addError($message, $fields = array(), $values = array()): static
    {
        $this->error_list->addError($message, $fields, $values);

        return $this;
    }

    function closePopup(): ResponseInterface
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

    function isCacheEnabled(): bool
    {
        return (bool)lmbEnv::get('LIMB_CONTROLLER_CACHE_ENABLED');
    }

    function _loadCache(): void
    {
        if ($this->isCacheEnabled() &&
            !self::$map_loaded &&
            file_exists($cache = lmbEnv::get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache')
        ) {
            self::$map_loaded = true;
            self::$action_template_map = unserialize(file_get_contents($cache));
        }
    }

    function _saveCache(): void
    {
        if ($this->isCacheEnabled()) {
            lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/locators/controller_action2tpl.cache',
                serialize(self::$action_template_map));
        }
    }

    function findTemplateForAction($action)
    {
        $this->_loadCache();

        $controller_name = get_class($this);

        if (isset(self::$action_template_map[$controller_name][$action]))
            return self::$action_template_map[$controller_name][$action];

        $template_format = $this->_getTemplatePath($action);

        $template_path = $this->findTemplateByAlias($template_format);
        self::$action_template_map[$controller_name][$action] = $template_path;

        $this->_saveCache();

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

    protected function isAjaxRequest($request): bool
    {
        if ($request->hasHeader('DNT'))
            return true;

        return $request->hasHeader('X-Requested-With') &&
            $request->getHeaderLine('X-Requested-With') == 'XMLHttpRequest';
    }
}
