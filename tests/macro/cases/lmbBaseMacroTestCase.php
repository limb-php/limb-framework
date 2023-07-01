<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\macro\cases;

use PHPUnit\Framework\TestCase;
use limb\macro\src\lmbMacroTemplate;
use limb\macro\src\lmbMacroConfig;
use limb\fs\src\lmbFs;

require_once('.setup.php');

class lmbBaseMacroTestCase extends TestCase
{
  public $base_dir;
  public $tpl_dir;
  public $cache_dir;
  public $tags_dir;
  public $filters_dir;

  function setUp(): void
  {
    $this->base_dir = lmb_var_dir() . '/tpl';
    $this->cache_dir = $this->base_dir . '/compiled';
    $this->tpl_dir = $this->base_dir;
    //$this->tags_dir = ['limb/macro/src/tags'];
    //$this->filters_dir = ['limb/macro/src/filters'];

    lmbFs::mkdir(lmb_var_dir());
    lmbFs::mkdir($this->base_dir);
    lmbFs::mkdir($this->tpl_dir);
    lmbFs::mkdir($this->cache_dir);
  }

  /**
   * @param string $file
   * @return lmbMacroTemplate
   */
  protected function _createMacro($file)
  {
    return new lmbMacroTemplate($file, $this->_createMacroConfig());
  }

  /**
   * @param string $code
   * @param string $name
   * @return string filename
   */
  protected function _createTemplate($code, $name = false)
  {
    if(!$name)
      $name = mt_rand() . '.phtml';
    $file = $this->tpl_dir . '/'. $name;
    file_put_contents($file, $code);

    return $file;
  }

  /**
   * @param string $code
   * @param string $name
   * @return lmbMacroTemplate
   */
  protected function _createMacroTemplate($code, $name = false)
  {
    $file = $this->_createTemplate($code, $name);
    return $this->_createMacro($file);
  }

  /**
   * @return lmbMacroConfig
   */
  protected function _createMacroConfig()
  {
    $config = array(
      'cache_dir' => $this->cache_dir,
      'forcecompile' => true,
      'forcescan' => true,
      'tpl_scan_dirs' =>  [$this->tpl_dir],
      'tags_scan_dirs' => ['../src/limb/*/src/macro', '../src/limb/macro/src/tags'],
      'filters_scan_dirs' => ['../src/limb/*/src/macro', '../src/limb/macro/src/filters']
    );
    return new lmbMacroConfig($config);
  }
}
