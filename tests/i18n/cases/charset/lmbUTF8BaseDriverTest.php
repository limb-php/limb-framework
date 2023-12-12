<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\i18n\cases\charset;

use limb\i18n\src\charset\lmbUTF8BaseDriver;
use limb\i18n\src\charset\lmbI18nString;

class lmbUTF8BaseDriverTest extends lmbMultiByteStringDriverTestBase
{
    function _createDriver()
    {
        return new lmbUTF8BaseDriver();
    }

    function testToUnicodeAndBackToUtf8()
    {
        $driver = $this->_createDriver();
        $unicode = $driver->toUnicode("Iñtërnâtiônàlizætiøn");

        $this->assertEquals($unicode, array(73, 241, 116, 235, 114, 110, 226, 116, 105, 244, 110, 224,
            108, 105, 122, 230, 116, 105, 248, 110));

        $this->assertEquals($driver->toUTF8($unicode), "Iñtërnâtiônàlizætiøn");
    }

    function test_utf8_to_win1251()
    {
        $this->assertEquals(lmbI18nString::utf8_to_win1251("тесты"), chr(0xF2) . chr(0xE5) . chr(0xF1) . chr(0xF2) . chr(0xFB));
    }

    function test_win1251_to_utf8()
    {
        $this->assertEquals(lmbI18nString::win1251_to_utf8(chr(0xF2) . chr(0xE5) . chr(0xF1) . chr(0xF2) . chr(0xFB)), "тесты");
    }
}
