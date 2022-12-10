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
use limb\web_cache\src\lmbFullPageCacheRule;
use limb\web_cache\src\lmbFullPageCachePolicy;
use limb\core\src\lmbObject;

class lmbFullPageCachePolicyTest extends TestCase
{
  protected $policy;

  function setUp(): void
  {
    $this->policy = new lmbFullPageCachePolicy();
  }

  function testFindRule()
  {
    $rule = new lmbFullPageCacheRule();

    $r1 = $this->createMock(lmbFullPageCacheRule::class);
    $r2 = $this->createMock(lmbFullPageCacheRule::class);
    $r3 = $this->createMock(lmbFullPageCacheRule::class);

    $request = new lmbObject();

    $r1->expects($this->once())->method('isSatisfiedBy')->with($request);
    $r1->setReturnValue('isSatisfiedBy', false, array($request));

    $r2->expects($this->once())->method('isSatisfiedBy')->with($request);
    $r2->setReturnValue('isSatisfiedBy', true, array($request));

    $r3->expects($this->never())->method('isSatisfiedBy');

    $this->policy->addRuleset($r1);
    $this->policy->addRuleset($r2);
    $this->policy->addRuleset($r3);

    $this->assertReference($r2, $this->policy->findRuleset($request));
  }
}
