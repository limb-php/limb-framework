<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases\charset;

use limb\i18n\src\charset\lmbUTF8MbstringDriver;

class lmbUTF8MbstringDriverTest extends lmbMultiByteStringDriverTestBase
{
    function _createDriver()
    {
        if (!function_exists('mb_strlen'))
            return null;

        return new lmbUTF8MbstringDriver();
    }
}
