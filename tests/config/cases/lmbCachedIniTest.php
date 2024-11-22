<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\config\cases;

use limb\config\src\lmbCachedIni;
use limb\fs\src\lmbFs;

require_once (dirname(__FILE__) . '/init.inc.php');

class lmbCachedIniTest extends lmbIniTest
{
    protected $cache_dir;

    function setUp(): void
    {
        parent::setUp();

        $this->cache_dir = lmb_var_dir() . '/ini/';
        lmbFs::rm($this->cache_dir);
    }

    function _createIni($contents)
    {
        file_put_contents($file = lmb_var_dir() . '/tmp_ini/' . mt_rand() . '.ini', $contents);
        return new lmbCachedIni($file, $this->cache_dir);
    }
}
