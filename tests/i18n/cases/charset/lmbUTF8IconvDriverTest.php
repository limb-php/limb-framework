<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases\charset;

use limb\i18n\src\charset\lmbUTF8IconvDriver;

class lmbUTF8IconvDriverTest extends lmbMultiByteStringDriverTestBase
{
    function _createDriver()
    {
        if (!function_exists('iconv_strlen'))
            return null;

        return new lmbUTF8IconvDriver();
    }
}
