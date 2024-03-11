<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
