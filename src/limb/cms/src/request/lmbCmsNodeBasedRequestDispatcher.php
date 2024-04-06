<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\request;

use limb\net\src\lmbUriHelper;
use limb\web_app\src\request\lmbRequestDispatcherInterface;
use limb\core\src\lmbEnv;
use limb\net\src\lmbUri;
use limb\cms\src\model\lmbCmsNode as Node;

/**
 * class lmbCmsNodeBasedRequestDispatcher.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsNodeBasedRequestDispatcher implements lmbRequestDispatcherInterface
{
    protected $node_class = Node::class;

    protected $path_offset;
    protected $base_path;

    function __construct($node_class = null, $path_offset = null, $base_path = null)
    {
        if ($node_class)
            $this->node_class = $node_class;

        $this->path_offset = lmbEnv::get('LIMB_HTTP_OFFSET_PATH');
        if (!is_null($path_offset))
            $this->path_offset = $path_offset;

        $this->base_path = lmbEnv::get('LIMB_HTTP_BASE_PATH');
        if (!is_null($base_path))
            $this->base_path = $base_path;
    }

    function dispatch($request)
    {
        $result = array();

        $uri = $request->getUri();
        $uri = $uri->withPath( lmbUriHelper::normalizePath($uri->getPath()) );
        $level = $this->_getHttpBasePathOffsetLevel($uri);

        if (!$node = $this->node_class::findByPath(lmbUriHelper::getPathFromLevel($uri, $level))) {
            return $result;
        }

        $result['controller'] = $node->getControllerName();

        if ($action = $request->get('action'))
            $result['action'] = $action;

        $result['node_id'] = $node->getId();

        return $result;
    }

    protected function _getHttpBasePathOffsetLevel(lmbUri $uri)
    {
        if (!$this->path_offset)
            return 0;

        $base_path_uri = new lmbUri(rtrim($this->base_path, '/'));
        $base_path_uri = $uri->withPath( lmbUriHelper::normalizePath($base_path_uri) );

        $level = 1;
        while ((lmbUriHelper::getPathElement($uri, $level) == lmbUriHelper::getPathElement($base_path_uri, $level)) &&
            ($level < $base_path_uri->countPath())) {
            $level++;
        }

        return $level;
    }
}
