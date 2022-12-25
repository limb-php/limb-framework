<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\view;

use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbToolkit;
use limb\macro\src\lmbMacroTemplate;
use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;

class lmbMacroTestCase extends TestCase
{
  protected $toolkit;
  
  function setUp(): void
  {
    $this->toolkit = lmbToolkit::save();

    lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/tpl');
    lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tpl/compiled');
  }
  
  function tearDown(): void
  {
    lmbToolkit::restore();
  }

  protected function _createMacro($file)
  {
    $base_dir = lmbEnv::get('LIMB_VAR_DIR') . '/tpl';
    $cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/tpl/compiled';
    $macro = new lmbMacroTemplate($file, $this->toolkit->getMacroConfig());
    return $macro;
  }

  protected function _createTemplate($code, $name)
  {
    $file = lmbEnv::get('LIMB_VAR_DIR') . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }

  protected function _createMacroTemplate($code, $name)
  {
    $file = $this->_createTemplate($code, $name);
    return $this->_createMacro($file);
  }
}
