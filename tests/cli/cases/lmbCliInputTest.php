<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\cli\cases;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\cli\src\lmbCliInput;
use limb\cli\src\lmbCliOption;
use limb\cli\src\lmbCliException;

class lmbCliInputTest extends TestCase
{
    function testReadEmpty()
    {
        $cli = new lmbCliInput();
        $this->assertEquals(array(), $cli->getOptions());
        $this->assertEquals(array(), $cli->getArguments());

        $this->assertNull($cli->getOption('f'));
        $this->assertNull($cli->getOptionValue('f'));
        $this->assertFalse($cli->hasOption('f'));
        $this->assertEquals('wow', $cli->getOptionValue('f', 'wow'));
        $this->assertNull($cli->getArgument(0));
        $this->assertEquals('wow', $cli->getArgument(0, 'wow'));
    }

    function testUseStringOptionsDescription()
    {
        $cli = new lmbCliInput('i|input=;b;foo=;c=');
        $opts = $cli->getOptions();

        $this->assertEquals($opts[0], new lmbCliOption('i', 'input', lmbCliOption::VALUE_REQ));
        $this->assertEquals($opts[1], new lmbCliOption('b'));
        $this->assertEquals($opts[2], new lmbCliOption('foo', lmbCliOption::VALUE_REQ));
        $this->assertEquals($opts[3], new lmbCliOption('c', lmbCliOption::VALUE_REQ));
    }

    function testUseStringOptionsDescriptionWithEndingSeparator()
    {
        $cli = new lmbCliInput('h|help;');
        $opts = $cli->getOptions();

        $this->assertEquals($opts[0], new lmbCliOption('h', 'help'));
    }

    function testReadSimpleOptionsWithArguments()
    {
        $argv = array('foo.php', '-f', 'wow', '--bar=1', 'foo', 'bar');

        $cli = new lmbCliInput('f=;bar=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals('wow', $cli->getOptionValue('f'));
        $this->assertEquals('1', $cli->getOptionValue('bar'));
        $this->assertEquals(array('foo', 'bar'), $cli->getArguments());
    }

    function testReadOptionsHoldingSpaces()
    {
        $argv = array('foo.php', '--foo', 'wow hey test', '-f', 'spaces spaces', '--bar', 1, 'foo', 'bar');

        $cli = new lmbCliInput('foo=;bar=;f=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals('wow hey test', $cli->getOptionValue('foo'));
        $this->assertEquals('spaces spaces', $cli->getOptionValue('f'));
        $this->assertEquals(1, $cli->getOptionValue('bar'));
        $this->assertEquals(array('foo', 'bar'), $cli->getArguments());
    }

    function testNoValueOptionValuesBecomeArguments()
    {
        $cli = new lmbCliInput('f');
        $this->assertTrue($cli->read(array('foo.php', '-f', 'foo', 'bar')));
        $this->assertEquals(array('foo', 'bar'), $cli->getArguments());
    }

    function testReadOptionValueRequiredError()
    {
        $cli = new lmbCliInput('f|foo=');
        $cli->throwException();

        try {
            $cli->read(array('foo.php', '--foo'));
            $this->fail();
        } catch (lmbCliException $e) {
        }

        $cli->throwException(false);
        $this->assertFalse($cli->read(array('foo.php', '-f')));
    }

    function testReadNoOptionValueError()
    {
        $cli = new lmbCliInput('f|foo');
        $cli->throwException();

        try {
            $cli->read(array('foo.php', '--foo=1'));
            $this->fail();
        } catch (lmbCliException $e) {
        }

        $cli->throwException(false);
        $this->assertFalse($cli->read(array('foo.php', '--foo', 'foo', 'bar')));
    }

    function testMinimumArgumentsError()
    {
        $cli = new lmbCliInput();
        $cli->setMinimumArguments(2);
        $this->assertFalse($cli->read(array('foo.php', 'wow')));
    }

    function testOfGetOptionValueDualism()
    {
        $argv = array('foo.php', '-f', 1, '--bar=4');

        $cli = new lmbCliInput('f|foo=;b|bar=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals(1, $cli->getOptionValue('f'));
        $this->assertEquals(1, $cli->getOptionValue('foo'));
        $this->assertEquals(4, $cli->getOptionValue('b'));
        $this->assertEquals(4, $cli->getOptionValue('bar'));
    }

    function testReadWithEqualSignPresent()
    {
        $argv = array('foo.php', '--foo=1', '-b', 2);

        $cli = new lmbCliInput('f|foo=;b|bar=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals(1, $cli->getOptionValue('f'));
        $this->assertEquals(2, $cli->getOptionValue('b'));
    }

    function testReadOptionsWithEqualSignMissing()
    {
        $argv = array('foo.php', '--foo', 1, '-b', 2);

        $cli = new lmbCliInput('f|foo=;b|bar=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals(1, $cli->getOptionValue('f'));
        $this->assertEquals(2, $cli->getOptionValue('b'));
    }

    function testReadMixedOptions()
    {
        $argv = array('foo.php', '--foo=1', '-b', 2, '--zoo', 3);

        $cli = new lmbCliInput('f|foo=;b|bar=;z|zoo=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals(1, $cli->getOptionValue('f'));
        $this->assertEquals(2, $cli->getOptionValue('b'));
        $this->assertEquals(3, $cli->getOptionValue('z'));
    }

    function testLongOptionsWithNonAlphabeticChars()
    {
        $argv = array('foo.php', '--foo-Bar=1', '--bar-foo_now', 2, '--zoo', 3);

        $cli = new lmbCliInput('f|foo-Bar=;b|bar-foo_now=;z|zoo=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals(1, $cli->getOptionValue('f'));
        $this->assertEquals(2, $cli->getOptionValue('b'));
        $this->assertEquals(3, $cli->getOptionValue('z'));
    }

    function testShortOptionsWithUppercaseChars()
    {
        $argv = array('foo.php', '-B', '-C', 2);

        $cli = new lmbCliInput('B;C=');

        $this->assertTrue($cli->read($argv));
        $this->assertTrue($cli->hasOption('B'));
        $this->assertEquals(2, $cli->getOptionValue('C'));
    }

    function testShortOptionsWithNumberChars()
    {
        $argv = array('foo.php', '-1', '-2');

        $cli = new lmbCliInput('1;2;3');

        $this->assertTrue($cli->read($argv));
        $this->assertTrue($cli->hasOption('1'));
        $this->assertTrue($cli->hasOption('2'));
        $this->assertFalse($cli->hasOption('3'));
    }

    function testReadMixedOptionsArgsComeFirst()
    {
        $argv = array('foo.php', 'arg1', '--opt1', 'opt1', 'arg2');
        $cli = new lmbCliInput('opt1=');

        $this->assertTrue($cli->read($argv));
        $this->assertEquals('opt1', $cli->getOptionValue('opt1'));
        $this->assertEquals(array('arg1', 'arg2'), $cli->getArguments());
    }

    function testShortOptionsGluing()
    {
        $argv = array('foo.php', '-ibk');

        $cli = new lmbCliInput('i;b;k');

        $this->assertTrue($cli->read($argv));
        $this->assertTrue($cli->hasOption('i'));
        $this->assertTrue($cli->hasOption('b'));
        $this->assertTrue($cli->hasOption('k'));
        $this->assertFalse($cli->hasOption('z'));
    }

    function testOptionsGluingWithLastValue()
    {
        $argv = array('foo.php', '-ibk', 2);

        $cli = new lmbCliInput('i;b;k=');

        $this->assertTrue($cli->read($argv));
        $this->assertNull($cli->getOptionValue('i'));
        $this->assertNull($cli->getOptionValue('b'));
        $this->assertEquals(2, $cli->getOptionValue('k'));
    }

    function testUseRelaxedMode()
    {
        $argv = array('foo.php', 'arg1', '--opt1', 'arg2', 'arg3');
        $cli = new lmbCliInput();
        $cli->strictMode(false);
        $this->assertTrue($cli->read($argv));
        $this->assertTrue($cli->hasOption('opt1'));
        $this->assertEquals(array('arg1', 'arg2', 'arg3'), $cli->getArguments());
    }
}
