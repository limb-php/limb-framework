<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_agent\cases;

use limb\web_agent\src\lmbWebServerCookie;
use PHPUnit\Framework\TestCase;

/**
 * @package web_agent
 * @version $Id: lmbWebServerCookieTest.php 39 2007-10-03 21:08:36Z
 */
class lmbWebServerCookieTest extends TestCase {

  function testCookie()
  {
    $cookie = new lmbWebServerCookie('sid=xzxsd; expires=date; path=/; domain=.test.ru; secure');

    $this->assertEquals($cookie->name, 'sid');
    $this->assertEquals($cookie->value, 'xzxsd');
    $this->assertEquals($cookie->expires, 'date');
    $this->assertEquals($cookie->path, '/');
    $this->assertEquals($cookie->domain, '.test.ru');
    $this->assertTrue($cookie->secure);
  }

}
