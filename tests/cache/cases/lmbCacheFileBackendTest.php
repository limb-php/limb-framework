<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
