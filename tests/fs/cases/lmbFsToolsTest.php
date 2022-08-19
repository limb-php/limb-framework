<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\fs\src\toolkit\lmbFsTools;
use limb\core\src\lmbEnv;

class lmbFsToolsTest extends UnitTestCase
{
  /**
   * @var lmbFsTools
   */
  protected $tools;

  function setUp()
  {
    $this->tools = new lmbFsTools();
  }

  function testGetFilesLocator_CacheConditions()
  {
     $old_mode = lmbEnv::get('LIMB_APP_MODE');
     $old_var_dir = lmbEnv::get('LIMB_VAR_DIR');
     lmbEnv::set('LIMB_APP_MODE', 'devel');
     lmbEnv::remove('LIMB_VAR_DIR');

     $this->assertIsA($this->tools->getFileLocator('foo','locator1'), 'lmbFileLocator');

     lmbEnv::set('LIMB_VAR_DIR', $old_var_dir);
     $this->assertIsA($this->tools->getFileLocator('foo','locator2'), 'lmbFileLocator');

     lmbEnv::set('LIMB_APP_MODE', 'production');
     $this->assertIsA($this->tools->getFileLocator('foo','locator3'), 'lmbCachingFileLocator');

     lmbEnv::set('LIMB_APP_MODE', $old_mode);
  }

}


