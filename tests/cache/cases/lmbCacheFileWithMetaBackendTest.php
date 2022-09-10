<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\cache\src\lmbCacheFileWithMetaBackend;

class lmbCacheFileWithMetaBackendTest extends lmbCacheFileBackendTest
{
  var $cache_dir;

  function _createPersisterImp()
  {
    $this->cache_dir = LIMB_VAR_DIR . '/cache';
    return new lmbCacheFileWithMetaBackend($this->cache_dir);
  }

}
