<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\filter;

use limb\FilterChain\lmbInterceptingFilterInterface;
use limb\Toolkit\lmbToolkit;
use Psr\Http\Message\ResponseInterface;

/**
 * class lmbCmsAccessPolicyFilter.
 *
 * @package cms
 * @version $Id$
 * @deprecated
 */
class lmbCmsAccessPolicyFilter implements lmbInterceptingFilterInterface
{
    protected $current_controller;

    function run($filter_chain, $request = null, $callback = null): ResponseInterface
    {
        $toolkit = lmbToolkit::instance();
        $this->current_controller = $toolkit->getDispatchedController();

        $user = $toolkit->getCmsUser();

        $current_path = '/' . ltrim($request->getUri()->getPath(), '/');

        $controller_name = $this->current_controller->getName();

        if (strpos($controller_name, 'admin') === 0) {
            if (!$user->isLoggedIn()) {
                $toolkit->flashMessage("Not authorized");
                $toolkit->redirectToRoute(['controller' => 'user', 'action' => 'login'], null, '?redirect=' . $current_path);

                return $callback;
            } elseif (!$this->_allowAccess($user)) {
                $toolkit->flashMessage("Forbidden");

                if ('display' != $this->current_controller->getCurrentAction())
                    $this->current_controller->closePopup();
                else
                    $toolkit->redirectToRoute(['controller' => 'admin', 'action' => 'display']);

                return $callback;
            }
        }

        return $filter_chain->next($request, $callback);
    }

    protected function _allowAccess($user)
    {
        $toolkit = lmbToolkit::instance();
        $access_rules = $toolkit->getConf('roles')->get($user->getRoleType());
        if (!is_array($access_rules))
            return false;

        $current_action = $this->current_controller->getCurrentAction();

        if (isset($access_rules['restricted_controllers']) &&
            in_array($this->current_controller->getName(), $access_rules['restricted_controllers']))
            return false;

        if (isset($access_rules['restricted_actions'][$this->current_controller->getName()]) &&
            in_array($current_action, $access_rules['restricted_actions'][$this->current_controller->getName()]))
            return false;

        return true;
    }
}
