<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cache\cases;

use limb\cache\src\lmbCacheFileBackend;
use limb\fs\src\lmbFs;
use limb\core\src\lmbEnv;

class lmbCacheFileBackendTest extends lmbCacheFileBackendTestCase
{
    protected $cache_dir;

    function _createPersisterImp()
    {
        $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
        return new lmbCacheFileBackend($this->cache_dir);
    }

}
