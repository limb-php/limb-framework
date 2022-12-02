<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_app\cases\plain\toolkit;

/**
 * @package web_app
 * @version $Id$
 */
use PHPUnit\Framework\TestCase;
use limb\toolkit\src\lmbMockToolsWrapper;
use limb\web_app\src\toolkit\lmbProfileTools;
use limb\toolkit\src\lmbToolkit;

class lmbProfileToolsTest extends TestCase
{

  function setUp(): void
  {
    lmbToolkit::save();
    lmbToolkit::merge(new lmbProfileTools());
    $this->toolkit = lmbToolkit :: instance();
  }

  function tearDown(): void
  {
    lmbToolkit::restore();
  }

  function testProfileStartPoint()
  {
    $this->assertTrue($this->toolkit->hasProfileStartPoint());
    $this->toolkit->setProfileStartPoint($time = microtime(true));
    $this->assertEquals($this->toolkit->getProfilePoint('__start__'), $time);
  }

  function testProfileEndPoint()
  {
    $this->assertFalse($this->toolkit->hasProfileEndPoint());
    $this->toolkit->setProfileEndPoint();
    $this->assertTrue($this->toolkit->hasProfileEndPoint());
    $this->toolkit->setProfileEndPoint($time = microtime(true));
    $this->assertEquals($this->toolkit->getProfilePoint('__end__'), $time);
  }

  function testGetSetProfilePoint()
  {
    $this->toolkit->setProfilePoint('first');
    $this->assertNotNull($this->toolkit->getProfilePoint('first'));
    $this->toolkit->setProfilePoint('first', $time = microtime(true));
    $this->assertEquals($this->toolkit->getProfilePoint('first'), $time);
  }

  function testClearProfilePoint()
  {
    $this->toolkit->setProfilePoint('first', $time = microtime(true));
    $this->assertEquals($this->toolkit->getProfilePoint('first'), $time);
    $this->toolkit->clearProfilePoint('first');
    try
    {
      $this->toolkit->getProfilePoint('first');
      $this->fail("point first must be cleared!");
    }
    catch (\Exception $e)
    {}
    try
    {
      $this->toolkit->clearProfilePoint('__start__');
      $this->fail("clearProfilePoint MUST NOT clear system points!");
    }
    catch (\Exception $e)
    {}
    try
    {
      $this->toolkit->clearProfilePoint('__end__');
      $this->fail("clearProfilePoint MUST NOT clear system points!");
    }
    catch (\Exception $e)
    {}
  }

  function testGetProfileTimeDiff()
  {
    $this->toolkit->setProfilePoint('first', $first = microtime(true));
    $this->toolkit->setProfilePoint('second', $second = $first + 100);
    $this->toolkit->setProfilePoint('third', $third = $second + 200);
    $this->assertEquals($this->toolkit->getProfileTimeDiff('__start__'), 0);
    $this->assertEquals($this->toolkit->getProfileTimeDiff('first'), $first - $this->toolkit->getProfilePoint('__start__'));
    $this->assertEquals($this->toolkit->getProfileTimeDiff('second'), $second - $first);
    // order of indexes doesn't matter
    $this->assertEquals($this->toolkit->getProfileTimeDiff('second', 'first'), $second - $first);
    $this->assertEquals($this->toolkit->getProfileTimeDiff('first', 'second'), $second - $first);
    $this->assertEquals($this->toolkit->getProfileTimeDiff('third', 'first'), $third - $first);
    try
    {
      $this->toolkit->getProfileTimeDiff('non-existing point');
      $this->fail('Non existing point must throw an exception');
    }
    catch (\Exception $e)
    {}
  }

  function testGetProfileTotal()
  {
    $this->toolkit->setProfileStartPoint($start = microtime(true));
    $this->toolkit->setProfileEndPoint($end = $start + 200);
    $this->assertEquals($this->toolkit->getProfileTotal(), $end - $start);
  }

  function testGetProfileTotalSetsEndPoint()
  {
    $this->assertFalse($this->toolkit->hasProfileEndPoint());
    $this->toolkit->getProfileTotal();
    $this->assertTrue($this->toolkit->getProfilePoint('__start__') < $this->toolkit->getProfilePoint('__end__'));
  }

  function testGetProfilePercentDiff()
  {
    $this->toolkit->setProfileStartPoint($start = microtime(true));
    $this->toolkit->setProfilePoint('first', $first = $start + 500);
    $this->toolkit->setProfilePoint('second', $second = $first + 100);
    $this->toolkit->setProfilePoint('third', $third = $second + 200);
    $this->toolkit->setProfileEndPoint($end = $third + 300);
    $total = $end - $start;
    $this->assertEquals($this->toolkit->getProfilePercentDiff('first'), 100 * ($first - $start) / $total);
    $this->assertEquals($this->toolkit->getProfilePercentDiff('second'), 100 * ($second - $first) / $total);
    // order of indexes doesn't matter
    $this->assertEquals($this->toolkit->getProfilePercentDiff('second', 'first'), 100 * ($second - $first) / $total);
    $this->assertEquals($this->toolkit->getProfilePercentDiff('first', 'second'), 100 * ($second - $first) / $total);
    $this->assertEquals($this->toolkit->getProfilePercentDiff('third', 'first'), 100 * ($third - $first) / $total);
  }

  function testAddProfileDiffView()
  {
    $this->toolkit->setProfilePoint('first', $first = microtime(true));
    $this->toolkit->setProfilePoint('second', $second = $first + 100);
    $this->toolkit->setProfilePoint('third', $third = $second + 200);
    $this->assertEquals($this->toolkit->getProfileDiffViews(), array());
    $this->toolkit->addProfileDiffView('first', 'third');
    $this->toolkit->addProfileDiffView('first', '__end__', 'from first to end');
    $views = array(
      array(
        'first_point' => 'first',
        'second_point' => 'third'
      ),
      'from first to end' => array(
        'first_point' => 'first',
        'second_point' => '__end__'
      )
    );
    $this->assertEquals($this->toolkit->getProfileDiffViews(), $views);
  }

  function testGetProfileStat()
  {
    $this->toolkit->setProfilePoint('first', $first = microtime(true));
    usleep(10000);
    $this->toolkit->setProfilePoint('second', $second = microtime(true));
    $stat = "<pre>{$this->toolkit->showProfileStatItem('first')}
{$this->toolkit->showProfileStatItem('second')}

Total: {$this->toolkit->getProfileTotal()} sec.</pre>";
    $this->assertEquals($this->toolkit->getProfileStat(false), $stat);
    $this->toolkit->addProfileDiffView('first', '__end__');
    $this->toolkit->addProfileDiffView('__start__', 'second', 'From start to second');
    $stat = "<pre>{$this->toolkit->showProfileStatItem('first')}
{$this->toolkit->showProfileStatItem('second')}

Custom profile points:
{$this->toolkit->showProfileStatItem('first', '__end__')}
{$this->toolkit->showProfileStatItem('__start__', 'second', 'From start to second')}

Total: {$this->toolkit->getProfileTotal()} sec.</pre>";
    $this->assertEquals($this->toolkit->getProfileStat(false), $stat);
  }
}
