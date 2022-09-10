<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbSerializable;
use limb\core\src\exception\lmbException;
use limb\core\src\lmbEnv;

class lmbSerializableTest extends TestCase
{
  function testSerializeUnserialize()
  {
    $stub = new SerializableTestStub();
    $container = new lmbSerializable($stub);
    $file = $this->_writeToFile(serialize($container));

    $this->assertEquals($this->_phpSerializedObjectCall($file, '->identify()'), $stub->identify());
    $this->assertEquals($this->_phpSerializedObjectCall($file, '->getChild()->identify()'), $stub->getChild()->identify());
    unlink($file);
  }

  function testRemoveIncludePathFromClassPath()
  {
    //generating class and placing it in a temp dir
    $var_dir = lmbEnv::get('LIMB_VAR_DIR');
    $class = 'Foo' . mt_rand();
    file_put_contents("$var_dir/" . $class . ".php", "<?php class $class { function say() {return 'hello';} }");

    //adding temp dir to include path
    $prev_inc_path = get_include_path();
    set_include_path($var_dir . PATH_SEPARATOR . get_include_path());

    //including class and serializing it
    include($class . '.php');
    $foo = new $class();
    $container = new lmbSerializable($foo);
    $file = $this->_writeToFile(serialize($container));

    //now moving generated class's file into subdir
    $new_dir = mt_rand();
    mkdir("$var_dir/$new_dir");
    rename("$var_dir/" . $class . ".php", "$var_dir/$new_dir/" . $class . ".php");

    //emulating new include path settings
    $this->assertEquals($this->_phpSerializedObjectCall($file, '->say()', "$var_dir/$new_dir"), $foo->say());

    set_include_path($prev_inc_path);
  }

  function testRemoveIncludePathWithTrailingSlashFromClassPath()
  {
    //generating class and placing it in a temp dir
    $var_dir = lmbEnv::get('LIMB_VAR_DIR');
    $class = 'Foo' . mt_rand();
    file_put_contents("$var_dir/" . $class . ".php", "<?php class $class { function say() {return 'hello';} }");

    //adding temp dir to include path
    $prev_inc_path = get_include_path();
    set_include_path("$var_dir//" . PATH_SEPARATOR . get_include_path());

    //including class and serializing it
    include($class . '.php');
    $foo = new $class();
    $container = new lmbSerializable($foo);
    $file = $this->_writeToFile(serialize($container));

    //now moving generated class's file into subdir
    $new_dir = mt_rand();
    mkdir("$var_dir/$new_dir");
    rename("$var_dir/" . $class . ".php", "$var_dir/$new_dir/" . $class . ".php");

    //emulating new include path settings
    $this->assertEquals($this->_phpSerializedObjectCall($file, '->say()', "$var_dir/$new_dir"), $foo->say());

    set_include_path($prev_inc_path);
  }

  function testSerializingUnserializeInternalClassThrowsException()
  {
      $function = function () {
          return "ABC";
      };

      $container = new lmbSerializable($function);

      try
      {
          serialize($container);
          $this->assertTrue(false);
      }
      catch(\Exception $e){
          $this->assertTrue(true);
      }

  }

  function _writeToFile($serialized)
  {
    $tmp_serialized_file = lmbEnv::get('LIMB_VAR_DIR') . '/serialized.' . mt_rand() . uniqid();
    file_put_contents($tmp_serialized_file, $serialized);
    return $tmp_serialized_file;
  }

  function _phpSerializedObjectCall($file, $call, $include_path = '')
  {
    $class_path = $this->_getClassPath(lmbSerializable::class);

    $cmd = "php -r \"require_once('$class_path');" .
           ($include_path != '' ? "set_include_path('$include_path');" : '') .
           "echo unserialize(file_get_contents('$file'))->getSubject()$call;\"";

    exec($cmd, $out, $ret);
    //var_dump($out);
    return implode("", $out);
  }

  function _getClassPath($class)
  {
    $ref = new ReflectionClass($class);
    return $ref->getFileName();
  }
}


