<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\fs\src\lmbFs;
use limb\cli\src\lmbCliResponse;
use limb\cli\src\lmbCliInput;
use limb\cli\src\lmbCliRunner;

class lmbCliRunnerTest extends TestCase
{
  var $tmp_dir;

  function setUp()
  {
    $this->tmp_dir = LIMB_VAR_DIR . '/tmp_cmd/';
    lmbFs :: mkdir($this->tmp_dir);
  }

  function tearDown()
  {
    lmbFs :: rm($this->tmp_dir);
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
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
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
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testCommandToClass()
  {
    $this->assertEquals(lmbCliRunner :: commandToClass('foo'), 'FooCliCmd');
    $this->assertEquals(lmbCliRunner :: commandToClass('foo_bar'), 'FooBarCliCmd');
    $this->assertEquals(lmbCliRunner :: commandToClass('foo-bar'), 'FooBarCliCmd');
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

    $this->assertEquals($runner->execute(), 0);
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

    $this->assertEquals($runner->execute(), 0);
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

    $this->assertEquals($runner->execute(), 1);
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

    $this->assertEquals($runner->execute(), 1);
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

    $this->_createCommandClass($cmd, 'function foo($argv){var_dump($argv);}');

    ob_start();
    $runner->execute();
    $str = ob_get_contents();
    ob_end_clean();

    $expected = <<<EOD
array(3) {
  [0]=>
  string(9) "--dry-run"
  [1]=>
  string(2) "-c"
  [2]=>
  string(3) "bar"
}

EOD;

    $this->assertEquals($expected, $str);
  }

  function _createCommandClass($name, $body='')
  {
    $class = lmbCliRunner :: commandToClass($name);

    $php = <<<EOD
<?php
class $class extends lmbCliBaseCmd
{
  $body
}
?>
EOD;
    file_put_contents(LIMB_VAR_DIR . '/tmp_cmd/' . $class . '.class.php', $php);
  }

  function _randomName()
  {
    return 'foo' . mt_rand();
  }
}


