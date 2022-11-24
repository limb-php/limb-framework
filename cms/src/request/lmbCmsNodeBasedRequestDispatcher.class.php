<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');
lmb_require('src/model/lmbCmsNode.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

/**
 * class lmbCmsNodeBasedRequestDispatcher.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsNodeBasedRequestDispatcher implements lmbRequestDispatcher
{
  protected $path_offset;
  protected $base_path;

  function __construct($path_offset = null, $base_path = null)
  {
    $this->path_offset = lmb_env_get('LIMB_HTTP_OFFSET_PATH');
    if(!is_null($path_offset))
      $this->path_offset = $path_offset;

    $this->base_path = lmb_env_get('LIMB_HTTP_BASE_PATH');
    if(!is_null($base_path))
      $this->base_path = $base_path;
  }

  function dispatch($request)
  {
    $result = array();

    $uri = $request->getUri();
    $uri->normalizePath();

    $level = $this->_getHttpBasePathOffsetLevel($uri);

    if(!$node = lmbCmsNode :: findByPath( $uri->getPathFromLevel($level) ))
       return $result;

    $result['controller'] = $node->getControllerName();

    if($action = $request->get('action'))
      $result['action'] = $action;

    $result['node_id'] = $node->getId();

    return $result;
  }

  protected function _getHttpBasePathOffsetLevel($uri)
  {
    if(!$this->path_offset)
      return 0;

    $base_path_uri = new lmbUri(rtrim($this->base_path, '/'));
    $base_path_uri->normalizePath();

    $level = 1;
    while(($uri->getPathElement($level) == $base_path_uri->getPathElement($level)) &&
          ($level < $base_path_uri->countPath()))
    {
      $level++;
    }

    return $level;
  }
}

