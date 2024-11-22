<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\request;

require_once dirname(__FILE__) . '/../../init.inc.php';

use PHPUnit\Framework\TestCase;
use limb\web_app\src\request\lmbInputFilter;

class lmbTestInputFilterStabRule
{
    function apply($data)
    {
    }
}

class lmbInputFilterTest extends TestCase
{
    function testAddFilter()
    {
        $input = array('foo' => 'Foo', 'bar' => 'Bar', 'zoo' => 'Zoo');
        $input_filter = new lmbInputFilter();

        $r1 = $this->createMock(lmbTestInputFilterStabRule::class);
        $r1
            ->expects($this->once())
            ->method('apply')
            ->with($input)
            ->willReturn($sub_res = array('foo' => 'Foo', 'bar' => 'Bar'), array($input));

        $r2 = $this->createMock(lmbTestInputFilterStabRule::class);
        $r2
            ->expects($this->once())
            ->method('apply')
            ->with($sub_res)
            ->willReturn($expected = array('foo' => 'Foo'), array($sub_res));

        $input_filter->addRule($r1);
        $input_filter->addRule($r2);

        $out = $input_filter->filter($input);
        $this->assertEquals($out, $expected);
    }
}
