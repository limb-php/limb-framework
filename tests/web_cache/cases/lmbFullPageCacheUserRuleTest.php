<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
use limb\web_cache\src\lmbFullPageCacheRequest;
use limb\web_cache\src\lmbFullPageCacheUser;
use limb\web_cache\src\lmbFullPageCacheUserRule;
use limb\net\src\lmbHttpRequest;

class lmbFullPageCacheUserRuleTest extends TestCase
{
  function testMatch()
  {
    $rule = new lmbFullPageCacheUserRule($groups = array('test1', 'test2'));

    $lmbHttpRequest = new lmbHttpRequest('whatever', array(), array());
    $user = new lmbFullPageCacheUser($groups);
    $request = new lmbFullPageCacheRequest($lmbHttpRequest, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testNomatch()
  {
    $rule = new lmbFullPageCacheUserRule($groups = array('test1', 'test2'));

    $lmbHttpRequest = new lmbHttpRequest('whatever', array(), array());
    $user = new lmbFullPageCacheUser(array('test1'));
    $request = new lmbFullPageCacheRequest($lmbHttpRequest, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testNegativeMatch()
  {
    $rule = new lmbFullPageCacheUserRule(array('!test2'));

    $lmbHttpRequest = new lmbHttpRequest('whatever', array(), array());
    $user = new lmbFullPageCacheUser(array('test1'));
    $request = new lmbFullPageCacheRequest($lmbHttpRequest, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testNegativeNonmatch()
  {
    $rule = new lmbFullPageCacheUserRule(array('!test2'));

    $lmbHttpRequest = new lmbHttpRequest('whatever', array(), array());
    $user = new lmbFullPageCacheUser(array('test2'));
    $request = new lmbFullPageCacheRequest($lmbHttpRequest, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testMixedGroupsMatch()
  {
    $rule = new lmbFullPageCacheUserRule($groups = array('test1', '!test2'));

    $lmbHttpRequest = new lmbHttpRequest('whatever', array(), array());
    $user = new lmbFullPageCacheUser(array('test1'));
    $request = new lmbFullPageCacheRequest($lmbHttpRequest, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }
}


