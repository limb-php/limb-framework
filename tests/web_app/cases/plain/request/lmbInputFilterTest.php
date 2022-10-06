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

Mock :: generate('lmbTestInputFilterStabRule', 'MockInputFilterRule');

class lmbInputFilterTest extends TestCase
{
  function testAddFilter()
  {
    $input = array('foo' => 'Foo', 'bar' => 'Bar', 'zoo' => 'Zoo');
    $input_filter = new lmbInputFilter();

    $r1 = new MockInputFilterRule();
    $r1->expectOnce('apply', array($input));
    $r1->setReturnValue('apply', $sub_res = array('foo' => 'Foo', 'bar' => 'Bar'), array($input));

    $r2 = new MockInputFilterRule();
    $r2->expectOnce('apply', array($sub_res));
    $r2->setReturnValue('apply', $expected = array('foo' => 'Foo'), array($sub_res));

    $input_filter->addRule($r1);
    $input_filter->addRule($r2);

    $out = $input_filter->filter($input);
    $this->assertEquals($out, $expected);
  }
}
