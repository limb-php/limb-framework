<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\web_cache\cases;

use PHPUnit\Framework\TestCase;
use limb\web_cache\src\lmbFullPageCacheRuleset;
use limb\web_cache\src\lmbFullPageCacheRule;
use limb\core\src\lmbObject;

class lmbFullPageCacheRulesetTest extends TestCase
{
  function testSetType()
  {
    $set = new lmbFullPageCacheRuleset();
    $this->assertTrue($set->isAllow());
    $this->assertFalse($set->isDeny());

    $set->setType(false);
    $this->assertFalse($set->isAllow());
    $this->assertTrue($set->isDeny());
  }

  function testIsSatisfied()
  {
    $r1 = $this->createMock(lmbFullPageCacheRule::class);
    $r2 = $this->createMock(lmbFullPageCacheRule::class);

    $r1->expectOnce('isSatisfiedBy', array($request = new lmbObject()));
    $r1->setReturnValue('isSatisfiedBy', true);

    $r2->expectOnce('isSatisfiedBy', array($request = new lmbObject()));
    $r2->setReturnValue('isSatisfiedBy', true);

    $set = new lmbFullPageCacheRuleset();
    $set->addRule($r1);
    $set->addRule($r2);

    $this->assertTrue($set->isSatisfiedBy($request));
  }

  function testIsNotSatisfied()
  {
    $r1 = $this->createMock(lmbFullPageCacheRule::class);
    $r2 = $this->createMock(lmbFullPageCacheRule::class);

    $r1->expectOnce('isSatisfiedBy', array($request = new lmbObject()));
    $r1->setReturnValue('isSatisfiedBy', false);

    $r2->expectNever('isSatisfiedBy');

    $set = new lmbFullPageCacheRuleset();
    $set->addRule($r1);
    $set->addRule($r2);

    $this->assertFalse($set->isSatisfiedBy($request));
  }
}
