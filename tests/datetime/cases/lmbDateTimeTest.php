<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/datetime/src/lmbDateTime.class.php');
lmb_require('limb/datetime/src/lmbDateTimeZone.class.php');

class FooDateTime extends lmbDateTime {}

class lmbDateTimeTest extends TestCase
{
  function testInvalidDateTime()
  {
    try
    {
      $date = new lmbDateTime(400, 500, 5000, 9000);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testNegativeStamp()
  {
  	$date = new lmbDateTime(-564634800);
  	$this->assertEquals($date->getDay(), 10);
  	$this->assertEquals($date->getMonth(), 2);
  	$this->assertEquals($date->getYear(), 1952);
  }

  function testInvalidDateTimeString()
  {
    try
    {
      $date = new lmbDateTime('baba-duba');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testValidate()
  {
    $this->assertTrue(lmbDateTime :: validate('2005-12-01 12:45:12'));
    $this->assertTrue(lmbDateTime :: validate('2005-12-01 12:45'));
    $this->assertTrue(lmbDateTime :: validate('2005-12-01'));
    $this->assertTrue(lmbDateTime :: validate('12:45:12'));
    $this->assertTrue(lmbDateTime :: validate('12:45'));
    $this->assertTrue(lmbDateTime :: validate(' 12:45:12 '));
  }

  function testValidateFalse()
  {
    $this->assertFalse(lmbDateTime :: validate('baba-duba'));
    $this->assertFalse(lmbDateTime :: validate('2005-12-01 12.'));
    $this->assertFalse(lmbDateTime :: validate(2006, 13, 11));
  }

  function testCreate()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals(lmbDateTime :: create(2005, 12, 1, 12, 45, 12), $date);

    $this->assertEquals($date->getDay(), 1);
    $this->assertEquals($date->getMonth(), 12);
    $this->assertEquals($date->getYear(), 2005);
    $this->assertEquals($date->getHour(), 12);
    $this->assertEquals($date->getMinute(), 45);
    $this->assertEquals($date->getSecond(), 12);
  }

  function testGetIsoDate()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->getIsoDate(), '2005-12-01 12:45:12');
  }

  function testGetIsoDateWithoutSeconds()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->getIsoDate(false), '2005-12-01 12:45');
  }

  function testGetIsoShortDate()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->getIsoShortDate(), '2005-12-01');
  }

  function testGetIsoTime()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->getIsoTime(), '12:45:12');
  }

  function testGetIsoTimeWithoutSeconds()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->getIsoTime(false), '12:45');
  }

  function testGetIsoShortTimeWithoutSeconds()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->getIsoShortTime(), '12:45');
  }

  function testToStringReturnsIsoDate()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->toString(), '2005-12-01 12:45:12');
  }

  function testStrftime()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->strftime('%m/%d/%y'), '12/01/05');
  }

  function testDate()
  {
    $date = new lmbDateTime(2005, 12, 1, 12, 45, 12);
    $this->assertEquals($date->date('m.d.y'), '12.01.05');
  }

  function testCreateByCopy()
  {
    $date = new lmbDateTime($sample = new lmbDateTime(2005, 12, 1, 12, 45, 12));
    $this->assertEquals(lmbDateTime :: create($sample), $date);

    $this->assertEquals($date, $sample);
  }

  function testCreateByIso()
  {
    $date = new lmbDateTime('2005-12-01  12:45:12');
    $this->assertEquals(lmbDateTime :: create('2005-12-01  12:45:12'), $date);

    $this->assertEquals($date->getDay(), 1);
    $this->assertEquals($date->getMonth(), 12);
    $this->assertEquals($date->getYear(), 2005);
    $this->assertEquals($date->getHour(), 12);
    $this->assertEquals($date->getMinute(), 45);
    $this->assertEquals($date->getSecond(), 12);

    $this->assertEquals($date->toString(), '2005-12-01 12:45:12');
  }

  function testCreateByIsoDateOnly()
  {
    $date = new lmbDateTime('2005-12-01');
    $this->assertEquals(lmbDateTime :: create('2005-12-01'), $date);

    $this->assertEquals($date->getDay(), 1);
    $this->assertEquals($date->getMonth(), 12);
    $this->assertEquals($date->getYear(), 2005);
    $this->assertEquals($date->getHour(), 0);
    $this->assertEquals($date->getMinute(), 0);
    $this->assertEquals($date->getSecond(), 0);

    $this->assertEquals($date->toString(), '2005-12-01 00:00:00');
  }

  function testCreateByIsoTimeOnly()
  {
    $date = new lmbDateTime('12:45:12');
    $this->assertEquals(lmbDateTime :: create('12:45:12'), $date);

    $this->assertEquals($date->getDay(), 0);
    $this->assertEquals($date->getMonth(), 0);
    $this->assertEquals($date->getYear(), 0);
    $this->assertEquals($date->getHour(), 12);
    $this->assertEquals($date->getMinute(), 45);
    $this->assertEquals($date->getSecond(), 12);

    $this->assertEquals($date->toString(), '0000-00-00 12:45:12');
  }

  function testCreateByIsoTimeWithSecondsOmitted()
  {
    $date = new lmbDateTime('12:45');
    $this->assertEquals(lmbDateTime :: create('12:45'), $date);

    $this->assertEquals($date->getDay(), 0);
    $this->assertEquals($date->getMonth(), 0);
    $this->assertEquals($date->getYear(), 0);
    $this->assertEquals($date->getHour(), 12);
    $this->assertEquals($date->getMinute(), 45);
    $this->assertEquals($date->getSecond(), 0);

    $this->assertEquals($date->toString(), '0000-00-00 12:45:00');
  }

  function testStampToIso()
  {
    $stamp = mktime(21, 45, 13, 12, 1, 2005);
    $iso = lmbDateTime :: stampToIso($stamp);
    $this->assertEquals($iso, '2005-12-01 21:45:13');
  }

  function testCreateByStamp()
  {
    $date = new lmbDateTime($stamp = mktime(21, 45, 13, 12, 1, 2005));
    $this->assertEquals(lmbDateTime :: create($stamp), $date);

    $this->assertEquals($date->getDay(), 1);
    $this->assertEquals($date->getMonth(), 12);
    $this->assertEquals($date->getYear(), 2005);
    $this->assertEquals($date->getHour(), 21);
    $this->assertEquals($date->getMinute(), 45);
    $this->assertEquals($date->getSecond(), 13);

    $this->assertEquals($date->toString(), '2005-12-01 21:45:13');
  }

  function testCreateByDays()
  {
    $date = new lmbDateTime('2005-12-01');
    $days = $date->getDateDays();
    $this->assertEquals(lmbDateTime :: createByDays($days), $date);
  }

  function testGetStamp()
  {
    $date = new lmbDateTime($stamp = mktime(21, 45, 13, 12, 1, 2005));
    $this->assertEquals($date->getStamp(), $stamp);
  }

  function testGetPhpDayOfWeekForSunday()
  {
    $date = new lmbDateTime('2005-01-16');
    $this->assertEquals($date->getPhpDayOfWeek(), 0);
  }

  function testGetIntlDayOfWeekForSunday()
  {
    $date = new lmbDateTime('2005-01-16');
    $this->assertEquals($date->getIntlDayOfWeek(), 6);
  }

  function testGetPhpDayOfWeekForMonday()
  {
    $date = new lmbDateTime('2005-01-17');
    $this->assertEquals($date->getPhpDayOfWeek(), 1);
  }

  function testGetIntlDayOfWeekForMonday()
  {
    $date = new lmbDateTime('2005-01-17');
    $this->assertEquals($date->getIntlDayOfWeek(), 0);
  }

  function testGetPhpDayOfWeekForSuturday()
  {
    $date = new lmbDateTime('2005-01-15');
    $this->assertEquals($date->getPhpDayOfWeek(), 6);
  }

  function testGetIntlDayOfWeekForSuturday()
  {
    $date = new lmbDateTime('2005-01-15');
    $this->assertEquals($date->getIntlDayOfWeek(), 5);
  }

  //in the two tests below we're testing a boundary situtation
  //for day of the week which happens in February
  function testGetPhpDayOfWeekMonthBeforeFebruary()
  {
    $date = new lmbDateTime('2005-01-20');
    $this->assertEquals($date->getPhpDayOfWeek(), 4);
  }

  function testGetPhpDayOfWeekMonthAfterFebruary()
  {
    $date = new lmbDateTime('2005-08-20');
    $this->assertEquals($date->getPhpDayOfWeek(), 6);
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
    $this->assertEquals($date->toString(), '2005-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2006-01-01 00:00:00');
  }

  function testSetMonth()
  {
    $date = new lmbDateTime('2005-01-01');
    $new_date = $date->setMonth(2);
    $this->assertEquals($date->toString(), '2005-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2005-02-01 00:00:00');
  }

  function testSetDay()
  {
    $date = new lmbDateTime('2005-01-01');
    $new_date = $date->setDay(2);
    $this->assertEquals($date->toString(), '2005-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2005-01-02 00:00:00');
  }

  function testSetHour()
  {
    $date = new lmbDateTime('2005-01-01');
    $new_date = $date->setHour(2);
    $this->assertEquals($date->toString(), '2005-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2005-01-01 02:00:00');
  }

  function testSetMinute()
  {
    $date = new lmbDateTime('2005-01-01');
    $new_date = $date->setMinute(2);
    $this->assertEquals($date->toString(), '2005-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2005-01-01 00:02:00');
  }

  function testSetSecond()
  {
    $date = new lmbDateTime('2005-01-01');
    $new_date = $date->setSecond(20);
    $this->assertEquals($date->toString(), '2005-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2005-01-01 00:00:20');
  }

  function TODO_testSetWeek()
  {
    $date = new lmbDateTime('2005-01-01');
    $new_date = $date->setWeek(2);
    $this->assertEquals($date->toString(), '2005-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2005-01-08 00:00:00');//???
  }

  function testSetTimeZone()
  {
    $date = new lmbDateTime('2005-01-01', 'Europe/Moscow');
    $new_date = $date->setTimeZone('UTC');
    $this->assertEquals($date->getTimeZone(), 'Europe/Moscow');
    $this->assertEquals($new_date->getTimeZone(), 'UTC');
  }

  function testAddYear()
  {
    $date = lmbDateTime :: create('2005-01-01')->addYear();
    $new_date = $date->addYear(-3);

    $this->assertEquals($date->toString(), '2006-01-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2003-01-01 00:00:00');
  }

  function testAddMonth()
  {
    $date = lmbDateTime :: create('2005-01-01')->addMonth();
    $new_date = $date->addMonth(-2);
    $this->assertEquals($date->toString(), '2005-02-01 00:00:00');
    $this->assertEquals($new_date->toString(), '2004-12-01 00:00:00');
  }

  function testAddWeek()
  {
    $date = lmbDateTime :: create('2005-01-01')->addWeek();
    $new_date = $date->addWeek(-3);
    $this->assertEquals($date->toString(), '2005-01-08 00:00:00');
    $this->assertEquals($new_date->toString(), '2004-12-18 00:00:00');
  }

  function testAddDay()
  {
    $date = lmbDateTime :: create('2005-01-01')->addDay();
    $new_date = $date->addDay(-33);
    $this->assertEquals($date->toString(), '2005-01-02 00:00:00');
    $this->assertEquals($new_date->toString(), '2004-11-30 00:00:00');
  }

  function testAddHour()
  {
    $date = lmbDateTime :: create('2005-01-01')->addHour();
    $new_date = $date->addHour(-3);
    $this->assertEquals($date->toString(), '2005-01-01 01:00:00');
    $this->assertEquals($new_date->toString(), '2004-12-31 22:00:00');
  }

  function testAddMinute()
  {
    $date = lmbDateTime :: create('2005-01-01')->addMinute();
    $new_date = $date->addMinute(-3);
    $this->assertEquals($date->toString(), '2005-01-01 00:01:00');
    $this->assertEquals($new_date->toString(), '2004-12-31 23:58:00');
  }

  function testAddSecond()
  {
    $date = lmbDateTime :: create('2005-01-01')->addSecond();
    $new_date = $date->addSecond(-61);
    $this->assertEquals($date->toString(), '2005-01-01 00:00:01');
    $this->assertEquals($new_date->toString(), '2004-12-31 23:59:00');
  }

  function testAddMixed()
  {
    $date = lmbDateTime :: create('2005-01-01')->addMonth()->addWeek(-1)->addDay(2)->addHour(2)->addSecond(-30)->addMinute(2);

    $this->assertEquals($date->toString(), '2005-01-27 02:01:30');
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
    $this->assertEquals($new_date->toString(), '2005-06-01 08:20:40');
  }

  function testToUTCWithDayLightSaving()
  {
    $date = new lmbDateTime('2005-01-01 12:20:40', 'Europe/Moscow');
    $new_date = $date->toUTC();
    $this->assertEquals($new_date->toString(), '2005-01-01 09:20:40');
  }

  function testIsInDaylightTime()
  {
    $date = new lmbDateTime('2005-01-01 12:20:40', 'Europe/Moscow');
    $this->assertFalse($date->isInDaylightTime());

    $date = new lmbDateTime('2005-06-01 12:20:40', 'Europe/Moscow');
    $this->assertTrue($date->isInDaylightTime());
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
    $this->assertEquals($date->getDayOfYear(), 1);

    $date = new lmbDateTime('2005-12-31 12:20:40');
    $this->assertEquals($date->getDayOfYear(), 365);
  }

  function testGetWeekOfYear()
  {
    $date = new lmbDateTime('2005-01-01 12:20:40');
    $this->assertEquals($date->getWeekOfYear(), 1);

    $date = new lmbDateTime('2005-01-06 12:20:40');
    $this->assertEquals($date->getWeekOfYear(), 1);

    $date = new lmbDateTime('2005-12-31 12:20:40');
    $this->assertEquals($date->getWeekOfYear(), 52);
  }

  function testCompare()
  {
    $d1 = new lmbDateTime('2005-01-01');
    $d2 = new lmbDateTime('2005-01-01');

    $this->assertEquals($d1->compare($d2), 0);
    $this->assertEquals($d1->addYear()->compare($d2), 1);
    $this->assertEquals($d1->compare($d2->addYear(2)), -1);
  }

  function testCompareThrowsExceptionForNonDate()
  {
    $d = new lmbDateTime();

    try
    {
      $d->compare('agrch');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
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
    $this->assertIsA($foo->addDay(), 'FooDateTime');
  }
}

