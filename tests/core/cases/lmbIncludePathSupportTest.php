<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\core\cases;

require_once ('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;

class lmbIncludePathSupportTest extends TestCase
{
  var $old_include_path;

  protected function setUp(): void
  {
    if(!is_dir(lmbEnv::get('LIMB_VAR_DIR')))
      mkdir(lmbEnv::get('LIMB_VAR_DIR'));

    if(!is_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp'))
      mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp');

    $this->old_include_path = get_include_path();
    set_include_path(lmbEnv::get('LIMB_VAR_DIR') . '/tmp' . PATH_SEPARATOR . get_include_path());
  }

  protected function tearDown(): void
  {
    $this->rm_dir(lmbEnv::get('LIMB_VAR_DIR') . '/tmp');
    set_include_path($this->old_include_path);
  }

  function testGlobFailedForRelativePath()
  {
    $_ = $this->_rnd();
    $files = lmbFs::glob("{$_}*.inc.php");
    $this->assertEquals($files, array());
  }

  function testGlobFailedForAbsolutePath()
  {
    $_ = $this->_rnd();
    $files = lmbFs::glob(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}*.inc.php");
    $this->assertEquals($files, array());
  }

  function testGlobForRelativePath()
  {
    $_ = $this->_rnd();

    file_put_contents(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}bar.inc.php", "bar");
    file_put_contents(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}foo.inc.php", "foo");
    file_put_contents(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}zoo.inc.php", "zoo");

    $files = lmbFs::glob("{$_}*.inc.php");

    sort($files);

    $this->assertEquals(sizeof($files), 3);
    $this->assertEquals(file_get_contents($files[0]), "bar");
    $this->assertEquals(file_get_contents($files[1]), "foo");
    $this->assertEquals(file_get_contents($files[2]), "zoo");
  }

  function testGlobForAbsolutePath()
  {
    $_ = $this->_rnd();

    file_put_contents(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}bar.inc.php", "bar");
    file_put_contents(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}foo.inc.php", "foo");
    file_put_contents(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}zoo.inc.php", "zoo");

    $files = lmbFs::glob(lmbEnv::get('LIMB_VAR_DIR') . "/tmp/{$_}*.inc.php");

    sort($files);

    $this->assertEquals(sizeof($files), 3);
    $this->assertEquals(file_get_contents($files[0]), "bar");
    $this->assertEquals(file_get_contents($files[1]), "foo");
    $this->assertEquals(file_get_contents($files[2]), "zoo");
  }

  function _rnd()
  {
    return mt_rand(1, 1000) . uniqid();
  }

  function rm_dir($path)
  {
    $dir = opendir($path);
    while($entry = readdir($dir))
    {
     if(is_file("$path/$entry"))
     {
       unlink("$path/$entry");
     }
     elseif(is_dir("$path/$entry") && $entry != '.' && $entry != '..')
     {
       $this->rm_dir("$path/$entry");
     }
    }

    closedir($dir);
    return rmdir($path);
  }
}

