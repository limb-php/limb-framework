<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases;

require_once('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbSerializable;
use limb\core\src\lmbEnv;
use tests\core\cases\src\SerializableTestStub;

class lmbSerializableTest extends TestCase
{
    function testSerializeUnserialize()
    {
        $stub = new SerializableTestStub();
        $container = new lmbSerializable($stub);
        $file = $this->_writeToFile(serialize($container));

        $this->assertEquals($stub->identify(), $this->_phpSerializedObjectCall($file, '->identify()'));
        $this->assertEquals($stub->getChild()->identify(), $this->_phpSerializedObjectCall($file, '->getChild()->identify()'));
        unlink($file);
    }

    function testSerializingUnserializeInternalClassThrowsException()
    {
        $function = function () {
            return "ABC";
        };

        $container = new lmbSerializable($function);

        try {
            serialize($container);
            $this->fail();
        } catch (\Exception $e) {
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
        //$class_path = $this->_getClassPath(lmbSerializable::class);
        $class_name = lmbSerializable::class;

        $cmd = "php -r \"require_once('vendor\autoload.php'); use $class_name;" .
            ($include_path != '' ? "set_include_path('$include_path');" : '') .
            "echo unserialize(file_get_contents('$file'))->getSubject()$call;\"";

        exec($cmd, $out, $ret);
        ///var_dump($cmd);
        //var_dump($out);
        return implode("", $out);
    }

    function _getClassPath($class)
    {
        $ref = new \ReflectionClass($class);
        return $ref->getFileName();
    }
}
