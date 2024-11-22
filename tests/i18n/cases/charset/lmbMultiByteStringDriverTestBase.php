<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases\charset;

use PHPUnit\Framework\TestCase;

abstract class lmbMultiByteStringDriverTestBase extends TestCase
{
    function _createDriver()
    {
        return null;
    }

    function test_substr()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("то просто тест", $driver->_substr("это просто тест", 1));
        $this->assertEquals("ääääσαφ", $driver->_substr("ääääσαφ", 0, 400));
        $this->assertEquals("ääσαφ", $driver->_substr("ääääσαφ", 2, 400));
        $this->assertEquals("äääσ", $driver->_substr("ääääσαφ", 1, 4));
        $this->assertEquals("φ", $driver->_substr("ääääσαφ", -1));
        $this->assertEquals("ääääσα", $driver->_substr("ääääσαφ", 0, -1));
        $this->assertEquals("äääσα", $driver->_substr("ääääσαφ", 1, -1));
    }

    function test_rtrim()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("τελευτατελ", $driver->_rtrim("τελευτατελ\0\n\n\t"));
        $this->assertEquals("τελευτατε", $driver->_rtrim("τελευτατε?++.*?", ".*?+"));
        //intervals stuff not working yet, and it's not clear how it should work
        //$this->assertEquals($driver->_rtrim("τελευτατε\n\t", "\0x00..\0x1F"), "τελευτατε");
    }

    function test_ltrim()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("τελευτατελ", $driver->_ltrim("\0\n\n\tτελευτατελ"));
        $this->assertEquals("τελευτατε", $driver->_ltrim("λτελευτατε", "λ"));
        $this->assertEquals("τελευτατε", $driver->_ltrim("?+.*+?τελευτατε", "?.*+"));
    }

    function test_trim()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("τελευτατελ", $driver->_trim(" \n\t\0 τελευτατελ\0\n\n\t"));
        $this->assertEquals("τελεpυτατελ", $driver->_trim("pτελεpυτατελp", "p"));
        $this->assertEquals("τελεpυτατε", $driver->_trim("pτελεpυτατελp", "pλ"));
        $this->assertEquals("τελευτατε", $driver->_trim("?*++?τελευτατε?+.+?", "?.+*"));
    }

    function test_str_replace()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("τευτατ",
            $driver->_str_replace("ελx", "", "τελxευτατελx"));
        $this->assertEquals("υελευυαυελ",
            $driver->_str_replace("τ", "υ", "τελευτατελ"));
        $search = array("τ", "υ");
        $this->assertEquals("λελελλαλελ",
            $driver->_str_replace($search, "λ", "τελευτατελ"));
        $replace = array("α", "ε");
        $this->assertEquals("αελεεαααελ",
            $driver->_str_replace($search, $replace, "τελευτατελ"));
    }

    function test_strlen()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals(10, $driver->_strlen("τελευτατελ"));
        $this->assertEquals(13, $driver->_strlen("τ\nελευτα τελ "));
    }

    function test_strpos()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals(5, $driver->_strpos("τελευτατελ", "τατ"));
        $this->assertEquals(1, $driver->_strpos("τελευτατελ", "ε"));
        $this->assertEquals(3, $driver->_strpos("τελευτατελ", "ε", 2));
    }

    function test_strrpos()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals(5, $driver->_strrpos("τελευτατελ", "τατ"));
        $this->assertEquals(8, $driver->_strrpos("τελευτατελ", "ε"));
        $this->assertEquals(8, $driver->_strrpos("τελευτατελ", "ε", 3));
    }

    function test_strtolower()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("тест", $driver->_strtolower("ТЕСТ"));
        $this->assertEquals("тест", $driver->_strtolower("тЕсТ"));
    }

    function test_strtoupper()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("ТЕСТ", $driver->_strtoupper("тест"));
        $this->assertEquals("ТЕСТ", $driver->_strtoupper("тЕсТ"));
    }

    function test_ucfirst()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("Тест", $driver->_ucfirst("тест"));
    }

    function test_ucfirst_Space()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $str = ' Iñtërnâtiônàlizætiøn';
        $ucfirst = ' Iñtërnâtiônàlizætiøn';
        $this->assertEquals($driver->_ucfirst($str), $ucfirst);
    }

    function test_ucfirst_Upper()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $str = 'Ñtërnâtiônàlizætiøn';
        $ucfirst = 'Ñtërnâtiônàlizætiøn';
        $this->assertEquals($driver->_ucfirst($str), $ucfirst);
    }

    function test_strcasecmp()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals(0, $driver->_strcasecmp("тест", "тест"));
        $this->assertEquals(0, $driver->_strcasecmp("тест", "ТесТ"));
        $this->assertTrue($driver->_strcasecmp("тест", "ТЕСТЫ") < 0);
        $this->assertTrue($driver->_strcasecmp("тесты", "ТЕСТ") > 0);
    }

    function test_substr_count()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $str = "это...просто тест, не стоит воспринимать это...всерьез";

        $this->assertEquals(2, $driver->_substr_count($str, "это..."));
    }

    function test_str_split()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $str = 'Iñtërnâtiônàlizætiøn';
        $array = array(
            'I', 'ñ', 't', 'ë', 'r', 'n', 'â', 't', 'i', 'ô', 'n', 'à', 'l', 'i',
            'z', 'æ', 't', 'i', 'ø', 'n',
        );
        $this->assertEquals($driver->_str_split($str), $array);
    }

    function test_str_split_Newline()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $str = "Iñtërn\nâtiônàl\nizætiøn\n";
        $array = array(
            'I', 'ñ', 't', 'ë', 'r', 'n', "\n", 'â', 't', 'i', 'ô', 'n', 'à', 'l', "\n", 'i',
            'z', 'æ', 't', 'i', 'ø', 'n', "\n",
        );
        $this->assertEquals($driver->_str_split($str), $array);
    }

    function test_preg_match()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals(1, $driver->_preg_match("/^(.)/", "тест", $matches));
        $this->assertEquals("т", $matches[1]);
    }

    function test_preg_match_all()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals(4, $driver->_preg_match_all("/(.)/", "тест", $matches));

        $this->assertEquals("т", $matches[1][0]);
        $this->assertEquals("е", $matches[1][1]);
        $this->assertEquals("с", $matches[1][2]);
        $this->assertEquals("т", $matches[1][3]);
    }

    function test_preg_replace()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("собаки", $driver->_preg_replace("/кошк./", "собаки", "кошки"));
    }

    function test_preg_replace_callback()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals("кошкi", $driver->_preg_replace_callback("/(кошк)(.)/",
            function ($m) {
                return $m[1] . "i";
            },
            "кошки"));
    }

    function test_preg_split()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $pieces = $driver->_preg_split("/д./", "кошки да собаки");
        $this->assertEquals("кошки ", $pieces[0]);
        $this->assertEquals(" собаки", $pieces[1]);
    }
}
