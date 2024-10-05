<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cli\cases;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\cli\lmbCliOption;
use limb\cli\lmbCliException;

class lmbCliOptionTest extends TestCase
{
    function testCreateException()
    {
        try {
            $opt = new lmbCliOption('foo', 'f');
            $this->fail();
        } catch (lmbCliException $e) {
            $this->assertTrue(true);
        }
    }

    function testCreateWithShortNameOnly()
    {
        $opt = new lmbCliOption('s', lmbCliOption::VALUE_REQ);
        $this->assertNull($opt->getLongName());
        $this->assertEquals('s', $opt->getShortName());
        $this->assertEquals(lmbCliOption::VALUE_REQ, $opt->getValueMode());
        $this->assertEquals('-s', $opt->toString());
    }

    function testCreateWithLongNameOnly()
    {
        $opt = new lmbCliOption('foo', lmbCliOption::VALUE_REQ);
        $this->assertNull($opt->getShortName());
        $this->assertEquals('foo', $opt->getLongName());
        $this->assertEquals(lmbCliOption::VALUE_REQ, $opt->getValueMode());
        $this->assertEquals('--foo', $opt->toString());
    }

    function testCreateWithBothNames()
    {
        $opt = new lmbCliOption('f', 'foo', lmbCliOption::VALUE_REQ);
        $this->assertEquals('f', $opt->getShortName());
        $this->assertEquals('foo', $opt->getLongName());
        $this->assertEquals(lmbCliOption::VALUE_REQ, $opt->getValueMode());
        $this->assertEquals('-f|--foo', $opt->toString());
    }

    function testDefaultValueMode()
    {
        $opt = new lmbCliOption('s');
        $this->assertEquals(lmbCliOption::VALUE_NO, $opt->getValueMode());

        $opt = new lmbCliOption('foo');
        $this->assertEquals(lmbCliOption::VALUE_NO, $opt->getValueMode());

        $opt = new lmbCliOption('f', 'foo');
        $this->assertEquals(lmbCliOption::VALUE_NO, $opt->getValueMode());
    }

    function testValueMode()
    {
        $opt = new lmbCliOption('s');
        $this->assertTrue($opt->isValueForbidden());

        $opt = new lmbCliOption('s', lmbCliOption::VALUE_REQ);
        $this->assertTrue($opt->isValueRequired());

        $opt = new lmbCliOption('s', lmbCliOption::VALUE_OPT);
        $this->assertTrue($opt->isValueOptional());
    }

    function testMatchSingleName()
    {
        $opt = new lmbCliOption('s');
        $this->assertTrue($opt->match('s'));
        $this->assertFalse($opt->match('b'));

        $opt = new lmbCliOption('foo');
        $this->assertTrue($opt->match('foo'));
        $this->assertFalse($opt->match('aaa'));
    }

    function testMatchAnyName()
    {
        $opt = new lmbCliOption('f', 'foo');
        $this->assertTrue($opt->match('foo'));
        $this->assertTrue($opt->match('f'));
    }

    function testGetSetValue()
    {
        $opt = new lmbCliOption('f');

        $this->assertNull($opt->getValue());

        $opt->setValue('wow');
        $this->assertEquals('wow', $opt->getValue());
    }

    function testIsPresent()
    {
        $opt = new lmbCliOption('f');
        $this->assertFalse($opt->isPresent());

        $opt->touch();
        $this->assertTrue($opt->isPresent());
    }

    function testIsPresentAfterSettingValue()
    {
        $opt = new lmbCliOption('f');
        $this->assertFalse($opt->isPresent());

        $opt->setValue(1);
        $this->assertTrue($opt->isPresent());
    }

    function testValidateRequiredValue()
    {
        $opt = new lmbCliOption('f', lmbCliOption::VALUE_REQ);

        try {
            $opt->validate();
            $this->fail();
        } catch (lmbCliException $e) {
            $this->assertTrue(true);
        }
    }

    function testValidateForbiddenValue()
    {
        $opt = new lmbCliOption('f', lmbCliOption::VALUE_NO);
        $opt->validate(); //should pass

        $opt->setValue(1);

        try {
            $opt->validate();
            $this->fail();
        } catch (lmbCliException $e) {
            $this->assertTrue(true);
        }
    }

}
