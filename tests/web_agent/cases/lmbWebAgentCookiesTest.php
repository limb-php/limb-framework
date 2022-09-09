<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/web_agent/src/lmbWebAgentCookies.class.php');

/**
 * @package web_agent
 * @version $Id: lmbWebAgentCookiesTest.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebAgentCookiesTest extends TestCase {

  function testGetSetCookie()
  {
    $cookies = new lmbWebAgentCookies(array('test1' => 'val1', 'test2' => 'val2'));

    $this->assertEquals($cookies->get('test1'), 'val1');
    $this->assertEquals($cookies->get('test2'), 'val2');

    $cookies->set('cookie3', 'val3');
    $this->assertEquals($cookies->get('cookie3'), 'val3');
  }

  function testHasCookie()
  {
    $cookies = new lmbWebAgentCookies(array('test1' => 'val1', 'test2' => 'val2'));

    $this->assertTrue($cookies->has('test1'));
    $this->assertFalse($cookies->has('cookie3'));
  }

  function testIteration()
  {
    $cookies = new lmbWebAgentCookies(array('test1' => 'val1', 'test2' => 'val2'));

    $n = 1;
    foreach($cookies as $name => $val)
    {
      $this->assertEquals($name, 'test'.$n);
      $this->assertEquals($val, 'val'.$n);
      $n ++;
    }
    $this->assertEquals($n, 3);
  }

  function testClean()
  {
    $cookies = new lmbWebAgentCookies(array('test1' => 'val1', 'test2' => 'val2'));

    $cookies->clean();
    $this->assertFalse($cookies->has('test1'));
    $this->assertFalse($cookies->has('test2'));
  }

  function testExport()
  {
    $cookies = new lmbWebAgentCookies(array('test1' => 'val1', 'test2' => 'val2'));

    $this->assertEquals($cookies->export(), 'test1=val1; test2=val2');
  }


  function testHasCookies()
  {
    $cookies = new lmbWebAgentCookies();

    $this->assertFalse($cookies->hasCookies());
    $cookies->set('cookie3', 'val3');
    $this->assertTrue($cookies->hasCookies());
  }
}
