<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use limb\core\src\lmbEnv;
use tests\macro\cases\lmbBaseMacroTestCase;
use limb\macro\src\compiler\lmbMacroCodeWriter;
use limb\macro\src\compiler\lmbMacroTemplateExecutor;

class lmbMacroCodeWriterTest extends lmbBaseMacroTestCase
{
    protected $writer;

    function setUp(): void
    {
        parent::setUp();

        $this->class = 'Foo' . mt_rand();
        $this->writer = new lmbMacroCodeWriter($this->class);
    }

    function testRenderEmpty()
    {
        $object = $this->_instantiate();
        $this->assertInstanceOf(lmbMacroTemplateExecutor::class, $object);
        $this->assertNull($object->render());
    }

    function testWritePHP()
    {
        $this->writer->writePHP('return "Hello World!";');
        $object = $this->_instantiate();
        $this->assertEquals('Hello World!', $object->render());
    }

    function testWriteHTML()
    {
        $this->writer->writeHTML('<p>Hello World!</p>');
        $this->assertEquals('<p>Hello World!</p>', $this->_render());
    }

    function testSwithBetweenPHPAndHTML()
    {
        $this->writer->writePHP('echo ("Hello World!");');
        $this->writer->writeHTML('<p>Hello World!</p>');
        $this->writer->writePHP('echo ("Hello World!");');

        $this->assertEquals("Hello World!<p>Hello World!</p>Hello World!", $this->_render());
    }

    function testFunction()
    {
        $params = array('$a', '$b');
        $func = $this->writer->beginFunction('tpl' . mt_rand(), $params);
        $this->writer->writePHP('echo $a . $b;');
        $this->writer->endFunction();
        $this->writer->writePHP("$func('a', 'b');");

        $this->assertEquals('ab', $this->_render());
    }

    function testMethod()
    {
        $params = array('$a', '$b');
        $func = $this->writer->beginMethod('tpl' . mt_rand(), $params);
        $this->writer->writePHP('echo $a . $b;');
        $this->writer->endMethod();
        $this->writer->writePHP("\$this->$func('a', 'b');");

        $this->assertEquals('ab', $this->_render());
    }

    function testNestedMethods()
    {
        $params = array('$a', '$b');
        //inside fooxxx method
        $foo = $this->writer->beginMethod('foo' . mt_rand(), $params);

        //inside barxxx method, note, we're inside fooxxx as well
        $bar = $this->writer->beginMethod('bar' . mt_rand(), $params);
        $this->writer->writePHP('return $b . $a;');
        $this->writer->endMethod();

        $this->writer->writePHP('return $a . $b . ');//contecanating with barxxx method
        $this->writer->writePHP("\$this->$bar(\$a, \$b);");
        $this->writer->endMethod();

        $this->writer->writePHP("echo \$this->$foo('a', 'b');");

        $this->assertEquals('abba', $this->_render());
    }

    function testWriteIntoConstructor()
    {
        $bar = $this->writer->beginMethod('bar' . mt_rand());
        $this->writer->writePHP('echo "b-b-b";');
        $this->writer->endMethod();

        $foo = $this->writer->beginMethod('foo' . mt_rand());
        $this->writer->writePHP('echo "a-a-a";');
        $this->writer->endMethod();

        $this->writer->writePHP("\$this->$bar();");
        $this->writer->writeToInit("\$this->$foo();");

        $this->assertEquals('a-a-ab-b-b', $this->_render());
    }

    function testgenerateTempName()
    {
        $var = $this->writer->generateTempName();
        $this->assertMatchesRegularExpression('/[a-z][a-z0-9]*/i', $var);
    }

    function testGetSecondTempVariable()
    {
        $A = $this->writer->generateTempName();
        $B = $this->writer->generateTempName();
        $this->assertNotEquals($A, $B);
    }

    function testgenerateTempNamesMany()
    {
        for ($i = 1; $i <= 300; $i++) {
            $var = $this->writer->generateTempName();
            $this->assertMatchesRegularExpression('/[a-z][a-z0-9]*/i', $var);
        }
    }

    function _render()
    {
        $object = $this->_instantiate();
        ob_start();
        $object->render();
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    function _instantiate()
    {
        $this->_writeAndInclude($this->writer->renderCode());
        $class = $this->class;
        $object = new $class($this->_createMacroConfig());
        return $object;
    }

    function _writeAndInclude($code)
    {
        file_put_contents($file = lmbEnv::get('LIMB_VAR_DIR') . '/' . mt_rand() . '.php', $code);
        include($file);
        unlink($file);
    }
}
