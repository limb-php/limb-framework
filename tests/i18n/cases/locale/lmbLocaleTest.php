<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\i18n\cases\locale;

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

        $this->assertEquals($locale->getMonthName(0), 'January');
        $this->assertEquals($locale->getMonthName(11), 'December');
        $this->assertNull($locale->getMonthName(12));
    }

    function testGetDayName()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals($locale->getDayName(0, $short = false), 'Sunday');
        $this->assertEquals($locale->getDayName(0, $short = true), 'Sun');
        $this->assertEquals($locale->getDayName(6, $short = false), 'Saturday');
        $this->assertEquals($locale->getDayName(6, $short = true), 'Sat');
    }

    function testGetOtherOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals($locale->getCharset(), 'utf-8');
        $this->assertEquals($locale->getLanguageDirection(), 'ltr');
    }

    function testGetCountryOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals($locale->getCountryName(), 'USA');
        $this->assertEquals($locale->getCountryComment(), '');
    }

    function testGetLanguageOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals($locale->getLanguageName(), 'English (American)');
        $this->assertEquals($locale->getIntlLanguageName(), 'English (American)');
    }

    function testGetCurrencyOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals($locale->getCurrencySymbol(), '$');
        $this->assertEquals($locale->getCurrencyName(), 'US Dollar');
        $this->assertEquals($locale->getCurrencyShortName(), 'USD');
    }

    function testGetDateTimeFormatOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertEquals($locale->getTimeFormat(), '%H:%M:%S %p');
        $this->assertEquals($locale->getShortTimeFormat(), '%H:%M %p');
        $this->assertEquals($locale->getDateFormat(), '%A %d %B %Y');
        $this->assertEquals($locale->getShortDateFormat(), '%m/%d/%Y');
        $this->assertEquals($locale->getShortDateTimeFormat(), '%m/%d/%Y %H:%M:%S');
        $this->assertEquals($locale->getDateTimeFormat(), '%A %d %B %Y %H:%M:%S');
    }

    function testGetWeekDaysOptions()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertFalse($locale->isMondayFirst());
        $this->assertEquals($locale->getWeekDays(), array(0, 1, 2, 3, 4, 5, 6));
        $this->assertEquals($locale->getMonths(), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11));
        $this->assertEquals($locale->getWeekDayNames(), array('Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'));

        $this->assertEquals($locale->getWeekDayNames($short = true), array('Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat'));

        $this->assertEquals($locale->getMonthNames(), array('January',
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
            'December'));

        $this->assertEquals($locale->getMonthNames($short = true), array('Jan',
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
            'Dec'));

        $this->assertEquals($locale->getMeridiemName(10), 'am');
        $this->assertEquals($locale->getMeridiemName(22), 'pm');
    }
}
