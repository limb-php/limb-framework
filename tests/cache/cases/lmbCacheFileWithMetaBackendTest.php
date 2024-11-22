<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

use limb\cache\src\lmbCacheFileWithMetaBackend;
use limb\core\src\lmbEnv;

class lmbCacheFileWithMetaBackendTest extends lmbCacheFileBackendTestCase
{
    protected $cache_dir;

    function _createPersisterImp()
    {
        $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
        return new lmbCacheFileWithMetaBackend($this->cache_dir);
    }

}
