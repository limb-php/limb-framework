<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\cli\cases;

require_once ('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\fs\src\lmbFs;
use limb\cli\src\lmbCliResponse;
use limb\cli\src\lmbCliInput;
use limb\cli\src\lmbCliRunner;
use limb\core\src\exception\lmbException;
use limb\core\src\lmbEnv;

class lmbCliRunnerTest extends TestCase
{
  var $tmp_dir;

  function setUp(): void
  {
    $this->tmp_dir = lmbEnv::get('LIMB_VAR_DIR') . '/tmp_cmd/';
    lmbFs::mkdir($this->tmp_dir);
  }

  function tearDown(): void
  {
    lmbFs::rm($this->tmp_dir);
  }

  function testExecuteFailureNoCommand()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $runner = new lmbCliRunner($input, $output);
    $runner->returnOnExit();
    $runner->throwOnError();

    try
    {
      $runner->execute();
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testCantMapToCmdObject()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', 'foo'));

    $runner = new lmbCliRunner($input, $output);
    $runner->returnOnExit();
    $runner->throwOnError();

    try
    {
      $runner->execute();
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testCommandToClass()
  {
    $this->assertEquals('FooCliCmd', lmbCliRunner::commandToClass('foo'));
    $this->assertEquals('FooBarCliCmd', lmbCliRunner::commandToClass('foo_bar'));
    $this->assertEquals('FooBarCliCmd', lmbCliRunner::commandToClass('foo-bar'));
  }

  function testDefaultAction()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', $cmd = $this->_randomName()));

    $runner = new lmbCliRunner($input, $output);
    $runner->setCommandSearchPath($this->tmp_dir);
    $runner->returnOnExit();
    $runner->throwOnError();

    $this->_createCommandClass($cmd);

    $this->assertEquals(0, $runner->execute());
  }

  function testFallbackToDefaultAction()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', $cmd = $this->_randomName(), 'no-such-method'));

    $runner = new lmbCliRunner($input, $output);
    $runner->setCommandSearchPath($this->tmp_dir);
    $runner->returnOnExit();
    $runner->throwOnError();

    $this->_createCommandClass($cmd);

    $this->assertEquals(0, $runner->execute());
  }

  function testConcreteAction()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', $cmd = $this->_randomName(), 'foo'));

    $runner = new lmbCliRunner($input, $output);
    $runner->setCommandSearchPath($this->tmp_dir);
    $runner->returnOnExit();
    $runner->throwOnError();

    $this->_createCommandClass($cmd, 'function foo(){return 1;}');

    $this->assertEquals(1, $runner->execute());
  }

  function testSanitizeActionName()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', $cmd = $this->_randomName(), 'foo-bar'));

    $runner = new lmbCliRunner($input, $output);
    $runner->setCommandSearchPath($this->tmp_dir);
    $runner->returnOnExit();
    $runner->throwOnError();

    $this->_createCommandClass($cmd, 'function fooBar(){return 1;}');

    $this->assertEquals(1, $runner->execute());
  }

  function testPassArgvToAction()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->strictMode(false);
    $input->read(array('foo.php', $cmd = $this->_randomName(), 'foo', '--dry-run', '-c', 'bar'));

    $runner = new lmbCliRunner($input, $output);
    $runner->setCommandSearchPath($this->tmp_dir);
    $runner->returnOnExit();
    $runner->throwOnError();

    $this->_createCommandClass($cmd, 'function foo($argv){print_r($argv);}');

    ob_start();
    $runner->execute();
    $str = ob_get_contents();
    ob_end_clean();

    $expected = <<<EOD
Array(
    [0] => --dry-run
    [1] => -c
    [2] => bar
)
EOD;

      $expected = str_replace([PHP_EOL, "\n"], ["", ""], $expected);
      $str = str_replace([PHP_EOL, "\n", $cmd], ["", "", ""], $str);

      $this->assertEquals($expected, $str);
  }

  function _createCommandClass($name, $body='')
  {
    $class = lmbCliRunner::commandToClass($name);

    $php = <<<EOD
<?php
class $class extends limb\cli\src\lmbCliBaseCmd
{
  $body
}
?>
EOD;
    $class_path = lmbEnv::get('LIMB_VAR_DIR') . '/tmp_cmd/' . $class . '.php';

    file_put_contents($class_path, $php);

    include($class_path);
  }

  function _randomName()
  {
    return 'foo' . mt_rand();
  }
}
