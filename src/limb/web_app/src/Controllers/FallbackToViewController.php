<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\Controllers;

use Psr\Http\Message\RequestInterface;

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
    function performAction(RequestInterface $request, $dispatched_params = [])
    {
        $path = trim($request->getUri()->getPath(), '/');
        if ($template_path = $this->findTemplateByAlias($path)) {
            $view = $this->toolkit->createViewByTemplate($template_path);

            $response = response();
            $response->getBody()->write($view->render());

            return $response;
        } else
            return $this->forwardTo404();
    }

    function actionExists($action): bool
    {
        return true;
    }
}
