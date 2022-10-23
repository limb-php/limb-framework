<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\cache\cases;

require ('.setup.php');

use limb\cache\src\lmbCacheFileBackend;
use limb\fs\src\lmbFs;
use limb\core\src\lmbEnv;

class lmbCacheFileBackendTest extends lmbCacheBackendTest
{
  var $cache_dir;

  function _createPersisterImp()
  {
    $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
    return new lmbCacheFileBackend($this->cache_dir);
  }

  function testCachedDiskFiles()
  {
    $items = lmbFs::ls($this->cache_dir);
    $this->assertEquals(0, sizeof($items));

    $this->cache->set(1, $cache_value = 'value');

    $items = lmbFs::ls($this->cache_dir);
    $this->assertEquals(1, sizeof($items));

    $this->assertEquals($this->cache->get(1), $cache_value);

    $this->cache->flush();
    rmdir($this->cache_dir);
  }
}
