<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\config\cases;

use PHPUnit\Framework\TestCase;
use limb\config\src\lmbCachedIni;
use limb\fs\src\lmbFs;

require_once '.setup.php';

class lmbCachedIniCachingTest extends TestCase
{
  var $cache_dir;

  function setUp(): void
  {
    $this->cache_dir = lmb_var_dir() . '/ini/';
    lmbFs::rm($this->cache_dir);
    lmbFs::mkdir(lmb_var_dir() . '/tmp_ini/');
  }

  function tearDown(): void
  {
    lmbFs::rm(lmb_var_dir() . '/tmp_ini/');
    lmbFs::rm($this->cache_dir);
  }

  function _createIniFile($contents, &$override_file = null)
  {
    $name = mt_rand();
    $file = lmb_var_dir() . '/tmp_ini/' . $name . '.ini';
    $override_file = lmb_var_dir() . '/tmp_ini/' . $name . '.override.ini';
    file_put_contents($file, $contents);
    return $file;
  }

  function testCacheHit()
  {
    $file = $this->_createIniFile('test = 1');
    $ini1 = new lmbCachedIni($file, $this->cache_dir);

    $this->assertEquals(sizeof(scandir($this->cache_dir)), 3);//cache file + . and ..

    $this->assertEquals($ini1->get('test'), 1);

    file_put_contents($file, 'test = 2');//explicitly changing ini file
    touch($file, time() - 10);           //but making it look older than it is
    clearstatcache();

    $ini2 = new lmbCachedIni($file, $this->cache_dir);
    $this->assertEquals($ini2->get('test'), 1);
  }

  function testCacheMissFileWasModified()
  {
    $file = $this->_createIniFile('test = 1');
    $ini1 = new lmbCachedIni($file, $this->cache_dir);

    $this->assertEquals($ini1->get('test'), 1);

    file_put_contents($file, 'test = 2');
    touch($file, time() + 10);
    clearstatcache();

    $ini2 = new lmbCachedIni($file, $this->cache_dir);
    $this->assertEquals($ini2->get('test'), 2);
  }

  function testCacheMissOverrideFileWasModified()
  {
    $file = $this->_createIniFile('test = 1', $override_file);
    $ini1 = new lmbCachedIni($file, $this->cache_dir);

    $this->assertEquals($ini1->get('test'), 1);

    file_put_contents($override_file, 'test = 2');
    touch($override_file, time() + 10);

    $ini2 = new lmbCachedIni($file, $this->cache_dir);
    $this->assertEquals($ini2->get('test'), 2);
  }
}
