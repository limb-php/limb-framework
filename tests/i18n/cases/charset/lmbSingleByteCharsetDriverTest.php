<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases\charset;

use limb\i18n\src\charset\lmbSingleByteCharsetDriver;
use PHPUnit\Framework\TestCase;

class lmbSingleByteCharsetDriverTest extends TestCase
{
    function test_substr()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("ust a test", $driver->_substr("just a test", 1));
        $this->assertEquals("hello", $driver->_substr("hello", 0, 400));
        $this->assertEquals("ooba", $driver->_substr("foobar", 1, 4));
        $this->assertEquals("o", $driver->_substr("foo", -1));
        $this->assertEquals("fo", $driver->_substr("foo", 0, -1));
        $this->assertEquals("o", $driver->_substr("foo", 1, -1));
    }

    function test_rtrim()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("foo", $driver->_rtrim("foo\n\n\t"));
        $this->assertEquals("bar", $driver->_rtrim("bar?++.*?", ".*?+"));
    }

    function test_ltrim()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("foo", $driver->_ltrim("\n\n\tfoo"));
        $this->assertEquals("baz", $driver->_ltrim("?+.*+?baz", "?.*+"));
    }

    function test_trim()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("foo", $driver->_trim(" \n\t\0 foo\0\n\n\t"));
        $this->assertEquals("baz", $driver->_trim("pbazp", "p"));
        $this->assertEquals("bar", $driver->_trim("?*++?bar?+.+?", "?.+*"));
    }

    function test_str_replace()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("foobar",
            $driver->_str_replace("aaa", "", "fooaaabar"));
        $this->assertEquals("foobbz",
            $driver->_str_replace("a", "b", "foobaz"));
        $search = array("v", "x");
        $this->assertEquals("ddddddd",
            $driver->_str_replace($search, "d", "vxdddxv"));
        $replace = array("a", "w");
        $this->assertEquals("afoowbar",
            $driver->_str_replace($search, $replace, "vfooxbar"));
    }

    function test_strlen()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals(3, $driver->_strlen("foo"));
        $this->assertEquals(9, $driver->_strlen("\nfoo bar "));
    }

    function test_strpos()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals(0, $driver->_strpos("foo", "f"));
        $this->assertEquals(1, $driver->_strpos("foo", "o"));
        $this->assertEquals(2, $driver->_strpos("foo", "o", 2));
    }

    function test_strrpos()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals(2, $driver->_strrpos("foo", "o"));
        $this->assertEquals(2, $driver->_strrpos("foo", "o", 2));
    }

    function test_strtolower()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("test", $driver->_strtolower("TEST"));
        $this->assertEquals("test", $driver->_strtolower("tEsT"));
    }

    function test_strtoupper()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("TEST", $driver->_strtoupper("test"));
        $this->assertEquals("TEST", $driver->_strtoupper("tEsT"));
    }

    function test_ucfirst()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("Test", $driver->_ucfirst("test"));
    }

    function test_strcasecmp()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals(0, $driver->_strcasecmp("test", "test"));
        $this->assertEquals(0, $driver->_strcasecmp("test", "TesT"));
        $this->assertTrue($driver->_strcasecmp("test", "TESTS") < 0);
        $this->assertTrue($driver->_strcasecmp("tests", "TEST") > 0);
    }

    function test_substr_count()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $str = "This is a test";

        $this->assertEquals(2, $driver->_substr_count($str, "is"));
    }

    function test_str_split()
    {
        if (phpversion() < 5)
            return;

        $driver = new lmbSingleByteCharsetDriver();

        $str = 'Internationalization';
        $array = array(
            'I', 'n', 't', 'e', 'r', 'n', 'a', 't', 'i', 'o', 'n', 'a', 'l', 'i',
            'z', 'a', 't', 'i', 'o', 'n',
        );
        $this->assertEquals($driver->_str_split($str), $array);
    }

    function test_preg_match()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals(1, $driver->_preg_match("/^(.)/", "test", $matches));
        $this->assertEquals("t", $matches[1]);
    }

    function test_preg_match_all()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals(4, $driver->_preg_match_all("/(.)/", "test", $matches));

        $this->assertEquals("t", $matches[1][0]);
        $this->assertEquals("e", $matches[1][1]);
        $this->assertEquals("s", $matches[1][2]);
        $this->assertEquals("t", $matches[1][3]);
    }

    function test_preg_replace()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("dogs", $driver->_preg_replace("/cat./", "dogs", "cats"));
    }

    function test_preg_replace_callback()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $this->assertEquals("dogs", $driver->_preg_replace_callback("/(cat)(.)/",
            function ($m) {
                return "dog" . $m[2];
            },
            "cats"));
    }

    function test_preg_split()
    {
        $driver = new lmbSingleByteCharsetDriver();

        $pieces = $driver->_preg_split("/an./", "foo and bar");
        $this->assertEquals("foo ", $pieces[0]);
        $this->assertEquals(" bar", $pieces[1]);
    }
}
