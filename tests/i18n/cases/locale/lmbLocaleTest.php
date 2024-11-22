<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases\locale;

use PHPUnit\Framework\TestCase;
use limb\config\src\lmbIni;
use limb\i18n\src\locale\lmbLocaleSpec;
use limb\i18n\src\locale\lmbLocale;

class lmbLocaleTest extends TestCase
{
    function testGetLocaleSpec()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));
        $this->assertEquals($locale->getLocaleSpec(), new lmbLocaleSpec('en'));
    }

    function testGetMonthName()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals('January', $locale->getMonthName(0));
        $this->assertEquals('December', $locale->getMonthName(11));
        $this->assertNull($locale->getMonthName(12));
    }

    function testGetDayName()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals('Sunday', $locale->getDayName(0, $short = false));
        $this->assertEquals('Sun', $locale->getDayName(0, $short = true));
        $this->assertEquals('Saturday', $locale->getDayName(6, $short = false));
        $this->assertEquals('Sat', $locale->getDayName(6, $short = true));
    }

    function testGetOtherOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals('utf-8', $locale->getCharset());
        $this->assertEquals('ltr', $locale->getLanguageDirection());
    }

    function testGetCountryOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals('USA', $locale->getCountryName());
        $this->assertEquals('', $locale->getCountryComment());
    }

    function testGetLanguageOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals('English (American)', $locale->getLanguageName());
        $this->assertEquals('English (American)', $locale->getIntlLanguageName());
    }

    function testGetCurrencyOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals('$', $locale->getCurrencySymbol());
        $this->assertEquals('US Dollar', $locale->getCurrencyName());
        $this->assertEquals('USD', $locale->getCurrencyShortName());
    }

    function testGetDateTimeFormatOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals('%H:%M:%S %p', $locale->getTimeFormat());
        $this->assertEquals('%H:%M %p', $locale->getShortTimeFormat());
        $this->assertEquals('%A %d %B %Y', $locale->getDateFormat());
        $this->assertEquals('%m/%d/%Y', $locale->getShortDateFormat());
        $this->assertEquals('%m/%d/%Y %H:%M:%S', $locale->getShortDateTimeFormat());
        $this->assertEquals('%A %d %B %Y %H:%M:%S', $locale->getDateTimeFormat());
    }

    function testGetWeekDaysOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertFalse($locale->isMondayFirst());
        $this->assertEquals(array(0, 1, 2, 3, 4, 5, 6), $locale->getWeekDays());
        $this->assertEquals(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11), $locale->getMonths());
        $this->assertEquals(array('Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'), $locale->getWeekDayNames());

        $this->assertEquals(array('Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat'), $locale->getWeekDayNames($short = true));

        $this->assertEquals(array('January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'), $locale->getMonthNames());

        $this->assertEquals(array('Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'), $locale->getMonthNames($short = true));

        $this->assertEquals('am', $locale->getMeridiemName(10));
        $this->assertEquals('pm', $locale->getMeridiemName(22));
    }
}
