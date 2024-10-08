<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases;

use PHPUnit\Framework\TestCase;
use limb\i18n\charset\lmbI18nString;

class lmbI18NHelpersTest extends TestCase
{
    function testTraslitEmpty()
    {
        $this->assertEquals(lmbI18nString::translit_russian(''), '');
    }

    function testTranslitRussianText()
    {
        $str = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ь ы ъ э ю я ' .
            'А Б В Г Д Е Ё Ж З И Й К Л М Н О П Р С Т У Ф Х Ц Ч Ш Щ Ь Ы Ъ Э Ю Я';

        $expect = 'a b v g d e jo zh z i y k l m n o p r s t u f kh c ch sh sch  y  e yu ya ' .
            'A B V G D E JO ZH Z I Y K L M N O P R S T U F KH C CH SH SCH  Y  E YU YA';

        $this->assertEquals(lmbI18nString::translit_russian($str), $expect);
    }


    function testTranslitRusianTextWithEncoding()
    {
        $encoding = 'cp1251';
        $str = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ь ы ъ э ю я ' .
            'А Б В Г Д Е Ё Ж З И Й К Л М Н О П Р С Т У Ф Х Ц Ч Ш Щ Ь Ы Ъ Э Ю Я';

        $expect = 'a b v g d e jo zh z i y k l m n o p r s t u f kh c ch sh sch  y  e yu ya ' .
            'A B V G D E JO ZH Z I Y K L M N O P R S T U F KH C CH SH SCH  Y  E YU YA';

        $str = iconv('UTF-8', $encoding, $str);

        $this->assertEquals(lmbI18nString::translit_russian($str, $encoding), $expect);
    }
}
