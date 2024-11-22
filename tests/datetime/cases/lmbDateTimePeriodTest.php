<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\datetime\cases;

require_once (dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\datetime\src\lmbDateTimePeriod;
use limb\core\src\exception\lmbException;

class lmbDateTimePeriodTest extends TestCase
{
    function testInvalidPeriod()
    {
        try {
            $period = new lmbDateTimePeriod('2005-12-01 13:45:12', '2005-12-01 13:45:10');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testToString()
    {
        $p = new lmbDateTimePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
        $this->assertEquals($p->toString(), '2005-12-01 13:45:12 - 2005-12-01 13:46:00');
    }

    function testGetDuration()
    {
        $p = new lmbDateTimePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
        $this->assertEquals($p->getDuration(), 48);
    }

    function testIsEqual()
    {
        $p1 = new lmbDateTimePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
        $p2 = new lmbDateTimePeriod('2006-12-01 13:45:12', '2006-12-01 13:46:00');

        $this->assertTrue($p1->isEqual($p1));
        $this->assertFalse($p1->isEqual($p2));
    }

    function testIsInside()
    {
        $child = new lmbDateTimePeriod('2005-12-01 13:45:12', '2005-12-01 13:46:00');
        $parent = new lmbDateTimePeriod('2005-12-01 10:00:00', '2005-12-01 14:01:00');
        $intersect = new lmbDateTimePeriod('2005-12-01 11:00:00', '2005-12-01 15:01:00');

        $this->assertTrue($child->isInside($parent));
        $this->assertTrue($parent->includes($child));
        $this->assertFalse($parent->includes($intersect));
    }

    function testIntersects()
    {
        $period1 = new lmbDateTimePeriod("2005-09-05 14:40:00", "2005-09-05 14:41:50");
        $period2 = new lmbDateTimePeriod("2005-09-05 14:39:00", "2005-09-05 14:41:00");
        $period3 = new lmbDateTimePeriod("2006-09-05 14:39:00", "2006-09-05 14:41:00");

        $this->assertTrue($period1->intersects($period1));

        $this->assertTrue($period1->intersects($period2));
        $this->assertTrue($period2->intersects($period1));

        $this->assertFalse($period1->intersects($period3));
        $this->assertFalse($period3->intersects($period1));
    }
}
