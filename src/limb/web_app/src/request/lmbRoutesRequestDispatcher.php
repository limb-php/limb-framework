<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\request;

use limb\net\src\lmbUriHelper;
use limb\toolkit\src\lmbToolkit;
use limb\net\src\lmbUri;
use limb\core\src\lmbEnv;
use Psr\Http\Message\RequestInterface;

/**
 * class lmbRoutesRequestDispatcher.
 *
 * @package web_app
 * @version $Id: lmbRoutesRequestDispatcher.php 7676 2009-03-03 22:37:28Z
 */
class lmbRoutesRequestDispatcher implements lmbRequestDispatcherInterface
{
    protected $path_offset;
    protected $base_path;

    function __construct($path_offset = null, $base_path = null)
    {
        $this->path_offset = lmbEnv::get('LIMB_HTTP_OFFSET_PATH');
        if (!is_null($path_offset))
            $this->path_offset = $path_offset;

        $this->base_path = lmbEnv::get('LIMB_HTTP_BASE_PATH');
        if (!is_null($base_path))
            $this->base_path = $base_path;
    }

    function dispatch(RequestInterface $request)
    {
        $routes = lmbToolkit::instance()->getRoutes();

        $uri = $request->getUri()->withPath( lmbUriHelper::normalizePath($request->getUri()->getPath()) );

        $level = $this->_getHttpBasePathOffsetLevel($uri);

        $result = $routes->dispatch(lmbUriHelper::getPathFromLevel($uri, $level));

//        if ($action = $request->getAttribute('action'))
//            $result['action'] = $action;
//        if ($controller = $request->getAttribute('controller'))
//            $result['controller'] = $controller;

        return $result;
    }

    protected function _getHttpBasePathOffsetLevel(lmbUri $uri): int
    {
        if (!$this->path_offset)
            return 0;

        $base_path_uri = new lmbUri(rtrim($this->base_path, '/'));
        $base_path_uri = $base_path_uri->withPath( lmbUriHelper::normalizePath($base_path_uri->getPath()) );

        $level = 1;
        while ((lmbUriHelper::getPathElement($uri, $level) == lmbUriHelper::getPathElement($base_path_uri, $level)) &&
            ($level < $base_path_uri->countPath())) {
            $level++;
        }

        return $level;
    }
}
