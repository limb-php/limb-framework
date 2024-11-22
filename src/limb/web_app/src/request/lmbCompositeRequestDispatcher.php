<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\request;

use Psr\Http\Message\RequestInterface;

/**
 * class lmbCompositeRequestDispatcher.
 *
 * @package web_app
 * @version $Id: lmbCompositeRequestDispatcher.php 7486 2009-01-26 19:13:20Z
 */
class lmbCompositeRequestDispatcher implements lmbRequestDispatcherInterface
{
    protected $dispatchers;

    function dispatch(RequestInterface $request): array
    {
        foreach ($this->dispatchers as $dispatcher) {
            $result = $dispatcher->dispatch($request);
            if (isset($result['controller']))
                return $result;
        }

        return array();
    }

    function addDispatcher($dispatcher)
    {
        $this->dispatchers[] = $dispatcher;
    }
}
