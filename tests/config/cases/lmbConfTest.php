<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\config\cases;

use PHPUnit\Framework\TestCase;
use limb\config\src\lmbConf;
use limb\fs\src\lmbFs;
use limb\fs\src\exception\lmbFileNotFoundException;
use limb\core\src\exception\lmbNoSuchPropertyException;
use limb\core\src\exception\lmbInvalidArgumentException;

require_once '.setup.php';

class lmbConfTest extends TestCase
{
    protected function _getConfigPath($config_name)
    {
        return lmb_var_dir() . '/configs/' . $config_name;
    }

    function setUp(): void
    {
        $this->_createConfig('conf.php');
        $this->_createConfigWithReturn('conf2.php');
    }

    function tearDown(): void
    {
        lmbFs::rm($this->_getConfigPath('conf.php'));
        lmbFs::rm($this->_getConfigPath('conf2.php'));
        lmbFs::rm($this->_getConfigPath('conf.override.php'));
        lmbFs::rm($this->_getConfigPath('conf2.override.php'));
    }

    function _createConfig($name, $content = false)
    {
        if(!$content)
        {
            $content = <<<EOD
<?php
\$conf = array('foo' => 1,
              'bar' => 2);
EOD;
        }
        lmbFs::safeWrite($this->_getConfigPath($name), $content);
    }

    function _createConfigWithReturn($name, $content = false)
    {
        if(!$content)
        {
            $content = <<<EOD
<?php
return [
    'foo' => 100,
    'bar' => 200
];
EOD;
        }
        lmbFs::safeWrite($this->_getConfigPath($name), $content);
    }

    function _getConfig($name): lmbConf
    {
        return new lmbConf($this->_getConfigPath($name));
    }

    function testGet()
    {
        $conf = $this->_getConfig('conf.php');

        $this->assertEquals(1, $conf->get('foo'));
        $this->assertEquals(2, $conf->get('bar'));
    }

    function testGetReturnConfig()
    {
        $conf = $this->_getConfig('conf2.php');

        $this->assertEquals(100, $conf->get('foo'));
        $this->assertEquals(200, $conf->get('bar'));
    }

    function testOverride()
    {
        $content = <<<EOD
<?php
\$conf['foo'] = 10;
\$conf['acme'] = 3;
EOD;
        $this->_createConfig('conf.override.php', $content);

        $config = $this->_getConfig('conf.php');

        $this->assertEquals(10, $config->get('foo'));
        $this->assertEquals(2, $config->get('bar'));
        $this->assertEquals(3, $config->get('acme'));
    }

    function testOverrideReturnConfig()
    {
        $content = <<<EOD
<?php
return [
    'foo' => 1000,
    'acme' => 300
];
EOD;
        $this->_createConfig('conf2.override.php', $content);

        $config = $this->_getConfig('conf2.php');

        $this->assertEquals(1000, $config->get('foo'));
        $this->assertEquals(200, $config->get('bar'));
        $this->assertEquals(300, $config->get('acme'));
    }

  function testImplementsIterator()
  {
   $conf = $this->_getConfig('conf.php');
   $result = array();
   foreach ($conf as $key => $value)
     $result[$key] = $value;

   $this->assertEquals(array(
     'foo' => 1,
     'bar' => 2
   ), $result);
  }

  function testGetNotExistedFile()
  {
    try
    {
      $conf = new lmbConf('not_existed.php');
      $this->fail();
    }
    catch (lmbFileNotFoundException $e)
    {
      $this->assertTrue(true);
    }
  }

  function testGetNotExistedOption()
  {
    $conf = $this->_getConfig('conf.php');
    try
    {
      $conf->get('some_not_existed_option');
      $this->fail();
    }
    catch (lmbNoSuchPropertyException $e)
    {
        $this->assertTrue(true);
    }
  }

  function testGetWithoutOptionName()
  {
    $conf = $this->_getConfig('conf.php');
    try
    {
      $this->assertEquals(1, $conf->get(''));
      $this->fail();
    }
    catch(lmbInvalidArgumentException $e)
    {
        $this->assertTrue(true);
    }
  }
}
