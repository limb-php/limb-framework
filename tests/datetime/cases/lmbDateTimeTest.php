<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\datetime\cases;

require_once '.setup.php';

use PHPUnit\Framework\TestCase;
use limb\datetime\src\lmbDateTime;
use limb\datetime\src\lmbDateTimeZone;
use limb\core\src\exception\lmbException;

class FooDateTime extends lmbDateTime
{

}

class lmbDateTimeTest extends TestCase
{
    function testInvalidDateTime()
    {
        try {
            $date = new lmbDateTime(400, 500, 5000, 9000);
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testNegativeStamp()
    {
        $date = new lmbDateTime(-564634800);
        $this->assertEquals(10, $date->getDay());
        $this->assertEquals(2, $date->getMonth());
        $this->assertEquals(1952, $date->getYear());
    }

    function testInvalidDateTimeString()
    {
        try {
            $date = new lmbDateTime('baba-duba');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testValidate()
    {
        $this->assertTrue(lmbDateTime::validate('2005-12-01 12:45:12'));
        $this->assertTrue(lmbDateTime::validate('2005-12-01 12:45'));
        $this->assertTrue(lmbDateTime::validate('2005-12-01'));
        $this->assertTrue(lmbDateTime::validate('12:45:12'));
        $this->assertTrue(lmbDateTime::validate('12:45'));
        $this->assertTrue(lmbDateTime::validate(' 12:45:12 '));
    }

    function testValidateFalse()
    {
        $this->assertFalse(lmbDateTime::validate('baba-duba'));
        $this->assertFalse(lmbDateTime::validate('2005-12-01 12.'));
        $this->assertFalse(lmbDateTime::validate(2006, 13, 11));
    }

    /**
     * @throws lmbException
     */
    function testCreate()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals(lmbDateTime::create(2005, 12, 1, 12, 45, 12), $date);

        $this->assertEquals(1, $date->getDay());
        $this->assertEquals(12, $date->getMonth());
        $this->assertEquals(2005, $date->getYear());
        $this->assertEquals(12, $date->getHour());
        $this->assertEquals(45, $date->getMinute());
        $this->assertEquals(12, $date->getSecond());
    }

    function testGetIsoDate()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('2005-12-01 12:45:12', $date->getIsoDate());
    }

    function testGetIsoDateWithoutSeconds()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('2005-12-01 12:45', $date->getIsoDate(false));
    }

    function testGetIsoShortDate()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('2005-12-01', $date->getIsoShortDate());
    }

    function testGetIsoTime()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('12:45:12', $date->getIsoTime());
    }

    function testGetIsoTimeWithoutSeconds()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('12:45', $date->getIsoTime(false));
    }

    function testGetIsoShortTimeWithoutSeconds()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('12:45', $date->getIsoShortTime());
    }

    function testToStringReturnsIsoDate()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('2005-12-01 12:45:12', $date->toString());
    }

    function testStrftime()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('12/01/05', $date->strftime('m/d/y'));
    }

    function testDate()
    {
        $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
        $this->assertEquals('12.01.05', $date->date('m.d.y'));
    }

    function testCreateByCopy()
    {
        $date = new lmbDateTime($sample = new lmbDateTime(2005, 12, 1, 12, 45, 12));
        $this->assertEquals(lmbDateTime::create($sample), $date);

        $this->assertEquals($date, $sample);
    }

    function testCreateByIso()
    {
        $date = new lmbDateTime('2005-12-01  12:45:12');
        $this->assertEquals(lmbDateTime::create('2005-12-01  12:45:12'), $date);

        $this->assertEquals(1, $date->getDay());
        $this->assertEquals(12, $date->getMonth());
        $this->assertEquals(2005, $date->getYear());
        $this->assertEquals(12, $date->getHour());
        $this->assertEquals(45, $date->getMinute());
        $this->assertEquals(12, $date->getSecond());

        $this->assertEquals('2005-12-01 12:45:12', $date->toString());
    }

    function testCreateByIsoDateOnly()
    {
        $date = new lmbDateTime('2005-12-01');
        $this->assertEquals(lmbDateTime:: create('2005-12-01'), $date);

        $this->assertEquals(1, $date->getDay());
        $this->assertEquals(12, $date->getMonth());
        $this->assertEquals(2005, $date->getYear());
        $this->assertEquals(0, $date->getHour());
        $this->assertEquals(0, $date->getMinute());
        $this->assertEquals(0, $date->getSecond());

        $this->assertEquals('2005-12-01 00:00:00', $date->toString());
    }

    function testCreateByIsoTimeOnly()
    {
        $date = new lmbDateTime('12:45:12');
        $this->assertEquals(lmbDateTime::create('12:45:12'), $date);

        $this->assertEquals(0, $date->getDay());
        $this->assertEquals(0, $date->getMonth());
        $this->assertEquals(0, $date->getYear());
        $this->assertEquals(12, $date->getHour());
        $this->assertEquals(45, $date->getMinute());
        $this->assertEquals(12, $date->getSecond());

        $this->assertEquals('0000-00-00 12:45:12', $date->toString());
    }

    function testCreateByIsoTimeWithSecondsOmitted()
    {
        $date = new lmbDateTime('12:45');
        $this->assertEquals(lmbDateTime::create('12:45'), $date);

        $this->assertEquals(0, $date->getDay());
        $this->assertEquals(0, $date->getMonth());
        $this->assertEquals(0, $date->getYear());
        $this->assertEquals(12, $date->getHour());
        $this->assertEquals(45, $date->getMinute());
        $this->assertEquals(0, $date->getSecond());

        $this->assertEquals('0000-00-00 12:45:00', $date->toString());
    }

    function testStampToIso()
    {
        $stamp = mktime(21, 45, 13, 12, 1, 2005);
        $iso = lmbDateTime::stampToIso($stamp);
        $this->assertEquals('2005-12-01 21:45:13', $iso);
    }

    function testCreateByStamp()
    {
        $date = new lmbDateTime($stamp = mktime(21, 45, 13, 12, 1, 2005));
        $this->assertEquals(lmbDateTime::create($stamp), $date);

        $this->assertEquals(1, $date->getDay());
        $this->assertEquals(12, $date->getMonth());
        $this->assertEquals(2005, $date->getYear());
        $this->assertEquals(21, $date->getHour());
        $this->assertEquals(45, $date->getMinute());
        $this->assertEquals(13, $date->getSecond());

        $this->assertEquals('2005-12-01 21:45:13', $date->toString());
    }

    function testCreateByDays()
    {
        $date = new lmbDateTime('2005-12-01');
        $days = $date->getDateDays();
        $this->assertEquals(lmbDateTime::createByDays($days), $date);
    }

    function testGetStamp()
    {
        $date = new lmbDateTime($stamp = mktime(21, 45, 13, 12, 1, 2005));
        $this->assertEquals($date->getStamp(), $stamp);
    }

    function testGetPhpDayOfWeekForSunday()
    {
        $date = new lmbDateTime('2005-01-16');
        $this->assertEquals(0, $date->getPhpDayOfWeek());
    }

    function testGetIntlDayOfWeekForSunday()
    {
        $date = new lmbDateTime('2005-01-16');
        $this->assertEquals(6, $date->getIntlDayOfWeek());
    }

    function testGetPhpDayOfWeekForMonday()
    {
        $date = new lmbDateTime('2005-01-17');
        $this->assertEquals(1, $date->getPhpDayOfWeek());
    }

    function testGetIntlDayOfWeekForMonday()
    {
        $date = new lmbDateTime('2005-01-17');
        $this->assertEquals(0, $date->getIntlDayOfWeek());
    }

    function testGetPhpDayOfWeekForSuturday()
    {
        $date = new lmbDateTime('2005-01-15');
        $this->assertEquals(6, $date->getPhpDayOfWeek());
    }

    function testGetIntlDayOfWeekForSuturday()
    {
        $date = new lmbDateTime('2005-01-15');
        $this->assertEquals(5, $date->getIntlDayOfWeek());
    }

    //in the two tests below we're testing a boundary situation
    //for day of the week which happens in February
    function testGetPhpDayOfWeekMonthBeforeFebruary()
    {
        $date = new lmbDateTime('2005-01-20');
        $this->assertEquals(4, $date->getPhpDayOfWeek());
    }

    function testGetPhpDayOfWeekMonthAfterFebruary()
    {
        $date = new lmbDateTime('2005-08-20');
        $this->assertEquals(6, $date->getPhpDayOfWeek());
    }

    function testGetBeginOfDay()
    {
        $date = new lmbDateTime('2005-08-20 12:24:12');
        $this->assertEquals($date->getBeginOfDay(), new lmbDateTime('2005-08-20 00:00:00'));
    }

    function testGetEndOfDay()
    {
        $date = new lmbDateTime('2005-08-20 12:24:12');
        $this->assertEquals($date->getEndOfDay(), new lmbDateTime('2005-08-20 23:59:59'));
    }

    function testGetBeginOfWeek()
    {
        $date = new lmbDateTime('2005-01-20');
        $this->assertEquals($date->getBeginOfWeek(), new lmbDateTime('2005-01-17'));
    }

    function testGetBeginOfWeekForMonday()
    {
        $date = new lmbDateTime('2005-01-17');
        $this->assertEquals($date->getBeginOfWeek(), new lmbDateTime('2005-01-17'));
    }

    function testGetBeginOfWeekForSunday()
    {
        $date = new lmbDateTime('2005-01-16');
        $this->assertEquals($date->getBeginOfWeek(), new lmbDateTime('2005-01-10'));
    }

    function testGetEndOfWeek()
    {
        $date = new lmbDateTime('2005-01-20');
        $this->assertEquals($date->getEndOfWeek(), new lmbDateTime('2005-01-23 23:59:59'));
    }

    function testGetEndOfWeekForMonday()
    {
        $date = new lmbDateTime('2005-01-17');
        $this->assertEquals($date->getEndOfWeek(), new lmbDateTime('2005-01-23 23:59:59'));
    }

    function testGetEndOfWeekForSunday()
    {
        $date = new lmbDateTime('2005-01-16');
        $this->assertEquals($date->getEndOfWeek(), new lmbDateTime('2005-01-16 23:59:59'));
    }

    function testGetBeginOfMonth()
    {
        $date = new lmbDateTime('2005-08-20 12:24:12');
        $this->assertEquals($date->getBeginOfMonth(), new lmbDateTime('2005-08-01 00:00:00'));
    }

    function testGetEndOfMonth()
    {
        $date = new lmbDateTime('2007-05-09 12:24:12');
        $this->assertEquals($date->getEndOfMonth(), new lmbDateTime('2007-05-31 23:59:59'));
    }

    function testGetBeginOfYear()
    {
        $date = new lmbDateTime('2005-08-20 12:24:12');
        $this->assertEquals($date->getBeginOfYear(), new lmbDateTime('2005-01-01 00:00:00'));
    }

    function testGetEndOfYear()
    {
        $date = new lmbDateTime('2007-05-09 12:24:12');
        $this->assertEquals($date->getEndOfYear(), new lmbDateTime('2007-12-31 23:59:59'));
    }

    function testSetYear()
    {
        $date = new lmbDateTime('2005-01-01');
        $new_date = $date->setYear(2006);
        $this->assertEquals('2005-01-01 00:00:00', $date->toString());
        $this->assertEquals('2006-01-01 00:00:00', $new_date->toString());
    }

    function testSetMonth()
    {
        $date = new lmbDateTime('2005-01-01');
        $new_date = $date->setMonth(2);
        $this->assertEquals('2005-01-01 00:00:00', $date->toString());
        $this->assertEquals('2005-02-01 00:00:00', $new_date->toString());
    }

    function testSetDay()
    {
        $date = new lmbDateTime('2005-01-01');
        $new_date = $date->setDay(2);
        $this->assertEquals('2005-01-01 00:00:00', $date->toString());
        $this->assertEquals('2005-01-02 00:00:00', $new_date->toString());
    }

    function testSetHour()
    {
        $date = new lmbDateTime('2005-01-01');
        $new_date = $date->setHour(2);
        $this->assertEquals('2005-01-01 00:00:00', $date->toString());
        $this->assertEquals('2005-01-01 02:00:00', $new_date->toString());
    }

    function testSetMinute()
    {
        $date = new lmbDateTime('2005-01-01');
        $new_date = $date->setMinute(2);
        $this->assertEquals('2005-01-01 00:00:00', $date->toString());
        $this->assertEquals('2005-01-01 00:02:00', $new_date->toString());
    }

    function testSetSecond()
    {
        $date = new lmbDateTime('2005-01-01');
        $new_date = $date->setSecond(20);
        $this->assertEquals('2005-01-01 00:00:00', $date->toString());
        $this->assertEquals('2005-01-01 00:00:20', $new_date->toString());
    }

    function TODO_testSetWeek()
    {
        $date = new lmbDateTime('2005-01-01');
        $new_date = $date->setWeek(2);
        $this->assertEquals('2005-01-01 00:00:00', $date->toString());
        $this->assertEquals('2005-01-08 00:00:00', $new_date->toString());//???
    }

    function testSetTimeZone()
    {
        $date = new lmbDateTime('2005-01-01', 'Europe/Moscow');
        $new_date = $date->setTimeZone('UTC');
        $this->assertEquals('Europe/Moscow', $date->getTimeZone());
        $this->assertEquals('UTC', $new_date->getTimeZone());
    }

    function testAddYear()
    {
        $date = lmbDateTime::create('2005-01-01')->addYear();
        $new_date = $date->addYear(-3);

        $this->assertEquals('2006-01-01 00:00:00', $date->toString());
        $this->assertEquals('2003-01-01 00:00:00', $new_date->toString());
    }

    function testAddMonth()
    {
        $date = lmbDateTime::create('2005-01-01')->addMonth();
        $new_date = $date->addMonth(-2);
        $this->assertEquals('2005-02-01 00:00:00', $date->toString());
        $this->assertEquals('2004-12-01 00:00:00', $new_date->toString());
    }

    function testAddWeek()
    {
        $date = lmbDateTime::create('2005-01-01')->addWeek();
        $new_date = $date->addWeek(-3);
        $this->assertEquals('2005-01-08 00:00:00', $date->toString());
        $this->assertEquals('2004-12-18 00:00:00', $new_date->toString());
    }

    function testAddDay()
    {
        $date = lmbDateTime::create('2005-01-01')->addDay();
        $new_date = $date->addDay(-33);
        $this->assertEquals('2005-01-02 00:00:00', $date->toString());
        $this->assertEquals('2004-11-30 00:00:00', $new_date->toString());
    }

    function testAddHour()
    {
        $date = lmbDateTime::create('2005-01-01')->addHour();
        $new_date = $date->addHour(-3);
        $this->assertEquals('2005-01-01 01:00:00', $date->toString());
        $this->assertEquals('2004-12-31 22:00:00', $new_date->toString());
    }

    function testAddMinute()
    {
        $date = lmbDateTime::create('2005-01-01')->addMinute();
        $new_date = $date->addMinute(-3);
        $this->assertEquals('2005-01-01 00:01:00', $date->toString());
        $this->assertEquals('2004-12-31 23:58:00', $new_date->toString());
    }

    function testAddSecond()
    {
        $date = lmbDateTime::create('2005-01-01')->addSecond();
        $new_date = $date->addSecond(-61);
        $this->assertEquals('2005-01-01 00:00:01', $date->toString());
        $this->assertEquals('2004-12-31 23:59:00', $new_date->toString());
    }

    function testAddMixed()
    {
        $date = lmbDateTime::create('2005-01-01')->addMonth()->addWeek(-1)->addDay(2)->addHour(2)->addSecond(-30)->addMinute(2);

        $this->assertEquals('2005-01-27 02:01:30', $date->toString());
    }

    function testCreateWithTZ()
    {
        $date = new lmbDateTime(2005, 5, 3, 12, 10, 5, 'Europe/Moscow');
        $tz = $date->getTimeZoneObject();
        $this->assertEquals($tz, new lmbDateTimeZone('Europe/Moscow'));
    }

    function testCreateWithInvalidTZ()
    {
        $date = new lmbDateTime(2005, 5, 3, 12, 10, 5, 'bla-bla');
        $tz = $date->getTimeZoneObject();
        $this->assertEquals($tz, new lmbDateTimeZone('UTC'));
    }

    function testCreateTZByDateString()
    {
        $date = new lmbDateTime('2005-01-01', 'Europe/Moscow');
        $tz = $date->getTimeZoneObject();
        $this->assertEquals($tz, new lmbDateTimeZone('Europe/Moscow'));
    }

    function testCreateTZByDateTimeString()
    {
        $date = new lmbDateTime('2005-01-01 12:20:40', 'Europe/Moscow');
        $tz = $date->getTimeZoneObject();
        $this->assertEquals($tz, new lmbDateTimeZone('Europe/Moscow'));
    }

    function testIgnoreTZWhileCloning()
    {
        $date = new lmbDateTime(new lmbDateTime('2005-01-01 12:20:40', 'Europe/Moscow'), 'ya-hooo');
        $tz = $date->getTimeZoneObject();
        $this->assertEquals($tz, new lmbDateTimeZone('Europe/Moscow'));
    }

    function testToUTC()
    {
        $date = new lmbDateTime('2005-06-01 12:20:40', 'Europe/Moscow');
        $new_date = $date->toUTC();
        $this->assertEquals('2005-06-01 08:20:40', $new_date->toString());
    }

    /*function testToUTCWithDayLightSaving()
    {
      $date = new lmbDateTime('2005-01-01 12:20:40', 'Europe/Moscow');
      $new_date = $date->toUTC();
      $this->assertEquals('2005-01-01 09:20:40', $new_date->toString());
    }*/

    function testIsInDaylightTime()
    {
        $date = new lmbDateTime('2005-01-01 12:20:40', 'Europe/Moscow');
        $this->assertEquals(0, $date->isInDaylightTime());

        $date = new lmbDateTime('2005-06-01 12:20:40', 'Europe/Moscow');
        $this->assertEquals(1, $date->isInDaylightTime());
    }

    function testIsLeapYear()
    {
        $date = new lmbDateTime('2005-01-01 12:20:40');
        $this->assertFalse($date->isLeapYear());

        $date = new lmbDateTime('2004-01-01 12:20:40');
        $this->assertTrue($date->isLeapYear());
    }

    function testGetDayOfYear()
    {
        $date = new lmbDateTime('2005-01-01 12:20:40');
        $this->assertEquals(1, $date->getDayOfYear());

        $date = new lmbDateTime('2005-12-31 12:20:40');
        $this->assertEquals(365, $date->getDayOfYear());
    }

    function testGetWeekOfYear()
    {
        $date = new lmbDateTime('2005-01-01 12:20:40');
        $this->assertEquals(1, $date->getWeekOfYear());

        $date = new lmbDateTime('2005-01-06 12:20:40');
        $this->assertEquals(1, $date->getWeekOfYear());

        $date = new lmbDateTime('2005-12-31 12:20:40');
        $this->assertEquals(52, $date->getWeekOfYear());
    }

    function testCompare()
    {
        $d1 = new lmbDateTime('2005-01-01');
        $d2 = new lmbDateTime('2005-01-01');

        $this->assertEquals(0, $d1->compare($d2));
        $this->assertEquals(1, $d1->addYear()->compare($d2));
        $this->assertEquals(-1, $d1->compare($d2->addYear(2)));
    }

    function testCompareThrowsExceptionForNonDate()
    {
        $d = new lmbDateTime();

        try {
            $d->compare('agrch');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testStripTime()
    {
        $date = new lmbDateTime('2005-01-01 12:20:40');
        $this->assertEquals($date->stripTime(), new lmbDateTime('2005-01-01'));
    }

    function testStripDate()
    {
        $date = new lmbDateTime('2005-01-01 12:20:40');
        $this->assertEquals($date->stripDate(), new lmbDateTime('12:20:40'));
    }

    function testIsDateEqual()
    {
        $date1 = new lmbDateTime('2005-01-01 12:20:40');
        $date2 = new lmbDateTime('2005-01-01 13:20:40');
        $this->assertTrue($date1->isEqualDate($date2));
        $this->assertTrue($date2->isEqualDate($date1));
    }

    function testIsDateNotEqual()
    {
        $date1 = new lmbDateTime('2005-02-01 12:20:40');
        $date2 = new lmbDateTime('2005-01-01 13:20:40');
        $this->assertFalse($date1->isEqualDate($date2));
        $this->assertFalse($date2->isEqualDate($date1));
    }

    function testRightReturnedClassFromFluentInterface()
    {
        $foo = new FooDateTime();
        $this->assertInstanceOf(FooDateTime::class, $foo->addDay());
    }
}
