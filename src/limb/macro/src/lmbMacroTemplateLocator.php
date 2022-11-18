<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\macro\src;

use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbMacroTemplateLocator.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateLocator implements lmbMacroTemplateLocatorInterface 
{
  protected $cache_dir;
  protected $scan_dirs;
  protected $toolkit;

  function __construct(lmbMacroConfig $config)
  {
    $this->cache_dir = $config->cache_dir;
    $this->scan_dirs = $config->tpl_scan_dirs;    
    $this->toolkit = lmbToolkit::instance();
  }

  function locateSourceTemplate($file_name)
  {
    if(!lmbFs::isPathAbsolute($file_name))
      return $this->toolkit->tryFindFileByAlias($file_name, $this->scan_dirs, 'macro');
    elseif(file_exists($file_name))
      return $file_name;
  }

  function locateCompiledTemplate($file_name)
  {
    return $this->cache_dir . '/' . lmbMacroTemplate::encodeCacheFileName($file_name);
  }
}
