<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\web_app\cases\plain\request;

use PHPUnit\Framework\TestCase;
use limb\web_app\src\request\lmbInputFilter;

class lmbTestInputFilterStabRule
{
  function apply($data){}
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
