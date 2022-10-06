<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\config\cases;

use limb\config\src\lmbCachedIni;
use limb\fs\src\lmbFs;

require_once '.setup.php';

class lmbCachedIniTest extends lmbIniTest
{
  var $cache_dir;

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
