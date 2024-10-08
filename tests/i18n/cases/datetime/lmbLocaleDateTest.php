<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases\datetime;

use PHPUnit\Framework\TestCase;
use limb\i18n\datetime\lmbLocaleDateTime;
use limb\i18n\locale\lmbLocale;
use limb\config\lmbIni;
use limb\core\exception\lmbException;

class lmbLocaleDateTest extends TestCase
{
    function testCreateByLocaleString()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $date = lmbLocaleDateTime::localStringToDate($locale, 'Thursday 20 January 2005', '%A %d %B %Y');

        $this->assertEquals(1, $date->getMonth());
        $this->assertEquals(2005, $date->getYear());
        $this->assertEquals(20, $date->getDay());
    }

    function testCreateByAnotherLocaleString()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $date = lmbLocaleDateTime::localStringToDate($locale, 'Thu 20 Jan 2005', '%a %d %b %Y');

        $this->assertEquals(1, $date->getMonth());
        $this->assertEquals(2005, $date->getYear());
        $this->assertEquals(20, $date->getDay());
    }

    function testCreateByWrongStringThrowsException()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        try {
            $date = lmbLocaleDateTime::localStringToDate($locale, '02-29-2003', '%a %d %b %Y');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testIsLocalStringValid()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertTrue(lmbLocaleDateTime::isLocalStringValid($locale, 'Mon 01', '%a %d'));
    }

    function testIsLocalStringNotValid()
    {
        $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

        $this->assertFalse(lmbLocaleDateTime::isLocalStringValid($locale, '02-29-2003', '%a %d %b %Y'));
    }
}
