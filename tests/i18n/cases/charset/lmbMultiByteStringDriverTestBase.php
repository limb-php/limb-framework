<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\i18n\cases\charset;

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

        $this->assertEquals($driver->_substr("это просто тест", 1), "то просто тест");
        $this->assertEquals($driver->_substr("ääääσαφ", 0, 400), "ääääσαφ");
        $this->assertEquals($driver->_substr("ääääσαφ", 2, 400), "ääσαφ");
        $this->assertEquals($driver->_substr("ääääσαφ", 1, 4), "äääσ");
        $this->assertEquals($driver->_substr("ääääσαφ", -1), "φ");
        $this->assertEquals($driver->_substr("ääääσαφ", 0, -1), "ääääσα");
        $this->assertEquals($driver->_substr("ääääσαφ", 1, -1), "äääσα");
    }

    function test_rtrim()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_rtrim("τελευτατελ\0\n\n\t"), "τελευτατελ");
        $this->assertEquals($driver->_rtrim("τελευτατε?++.*?", ".*?+"), "τελευτατε");
        //intervals stuff not working yet, and it's not clear how it should work
        //$this->assertEquals($driver->_rtrim("τελευτατε\n\t", "\0x00..\0x1F"), "τελευτατε");
    }

    function test_ltrim()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_ltrim("\0\n\n\tτελευτατελ"), "τελευτατελ");
        $this->assertEquals($driver->_ltrim("λτελευτατε", "λ"), "τελευτατε");
        $this->assertEquals($driver->_ltrim("?+.*+?τελευτατε", "?.*+"), "τελευτατε");
    }

    function test_trim()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_trim(" \n\t\0 τελευτατελ\0\n\n\t"), "τελευτατελ");
        $this->assertEquals($driver->_trim("pτελεpυτατελp", "p"), "τελεpυτατελ");
        $this->assertEquals($driver->_trim("pτελεpυτατελp", "pλ"), "τελεpυτατε");
        $this->assertEquals($driver->_trim("?*++?τελευτατε?+.+?", "?.+*"), "τελευτατε");
    }

    function test_str_replace()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_str_replace("ελx", "", "τελxευτατελx"),
            "τευτατ");
        $this->assertEquals($driver->_str_replace("τ", "υ", "τελευτατελ"),
            "υελευυαυελ");
        $search = array("τ", "υ");
        $this->assertEquals($driver->_str_replace($search, "λ", "τελευτατελ"),
            "λελελλαλελ");
        $replace = array("α", "ε");
        $this->assertEquals($driver->_str_replace($search, $replace, "τελευτατελ"),
            "αελεεαααελ");
    }

    function test_strlen()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_strlen("τελευτατελ"), 10);
        $this->assertEquals($driver->_strlen("τ\nελευτα τελ "), 13);
    }

    function test_strpos()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_strpos("τελευτατελ", "τατ"), 5);
        $this->assertEquals($driver->_strpos("τελευτατελ", "ε"), 1);
        $this->assertEquals($driver->_strpos("τελευτατελ", "ε", 2), 3);
    }

    function test_strrpos()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_strrpos("τελευτατελ", "τατ"), 5);
        $this->assertEquals($driver->_strrpos("τελευτατελ", "ε"), 8);
        $this->assertEquals($driver->_strrpos("τελευτατελ", "ε", 3), 8);
    }

    function test_strtolower()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_strtolower("ТЕСТ"), "тест");
        $this->assertEquals($driver->_strtolower("тЕсТ"), "тест");
    }

    function test_strtoupper()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_strtoupper("тест"), "ТЕСТ");
        $this->assertEquals($driver->_strtoupper("тЕсТ"), "ТЕСТ");
    }

    function test_ucfirst()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEquals($driver->_ucfirst("тест"), "Тест");
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

        $this->assertEquals($driver->_strcasecmp("тест", "тест"), 0);
        $this->assertEquals($driver->_strcasecmp("тест", "ТесТ"), 0);
        $this->assertTrue($driver->_strcasecmp("тест", "ТЕСТЫ") < 0);
        $this->assertTrue($driver->_strcasecmp("тесты", "ТЕСТ") > 0);
    }

    function test_substr_count()
    {
        if (!is_object($driver = $this->_createDriver()))
            return;

        $str = "это...просто тест, не стоит воспринимать это...всерьез";

        $this->assertEquals($driver->_substr_count($str, "это..."), 2);
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
