<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\fs\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\fs\src\toolkit\lmbFsTools;
use limb\core\src\lmbEnv;
use limb\fs\src\lmbFileLocator;
use limb\fs\src\lmbCachingFileLocator;

class lmbFsToolsTest extends TestCase
{
    /**
     * @var lmbFsTools
     */
    protected $tools;

    function setUp(): void
    {
        $this->tools = new lmbFsTools();
    }

    function testGetFilesLocator_CacheConditions()
    {
        $old_mode = lmbEnv::get('LIMB_APP_MODE');
        $old_var_dir = lmbEnv::get('LIMB_VAR_DIR');
        lmbEnv::set('LIMB_APP_MODE', 'devel');
        lmbEnv::remove('LIMB_VAR_DIR');

        $this->assertInstanceOf(lmbFileLocator::class, $this->tools->getFileLocator('foo', 'locator1'));

        lmbEnv::set('LIMB_VAR_DIR', $old_var_dir);
        $this->assertInstanceOf(lmbFileLocator::class, $this->tools->getFileLocator('foo', 'locator2'));

        lmbEnv::set('LIMB_APP_MODE', 'production');
        $this->assertInstanceOf(lmbCachingFileLocator::class, $this->tools->getFileLocator('foo', 'locator3'));

        lmbEnv::set('LIMB_APP_MODE', $old_mode);
    }

}
