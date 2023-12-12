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
use limb\core\src\exception\lmbException;
use limb\net\src\lmbHttpResponse;
use limb\view\src\lmbJsonView;
use limb\view\src\lmbView;

/**
 * class lmbActionPerformingAndViewRenderingFilter.
 * @deprecated
 * @package web_app
 * @version $Id: lmbActionPerformingAndViewRenderingFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbActionPerformingAndViewRenderingFilter implements lmbInterceptingFilterInterface
{
    function run($filter_chain, $request = null, $callback = null)
    {
        $dispatched = lmbToolkit::instance()->getDispatchedController();
        if (!is_object($dispatched))
            throw new lmbException('Request is not dispatched yet! lmbDispatchedRequest not found in lmbToolkit!');

        $result = $dispatched->performAction($request);

        $response = lmbToolkit::instance()->getResponse();

        if ($result !== null) {
            if (is_a($result, lmbHttpResponse::class)) {
                $response = $result;
            } elseif ($response->isEmpty()) {
                if (is_a($result, lmbView::class)) {
                    $result = $result->render();
                } elseif (is_array($result)) {
                    $result = (new lmbJsonView($result))->render();
                }

                $response->write($result);
            }
        } elseif ($view = lmbToolkit::instance()->getView()) {
            if ($response->isEmpty()) {
                $response->write($view->render());
            }
        } else {
            throw new lmbException('Empty controller response');
        }

        return $filter_chain->next($request, $callback);
    }
}
