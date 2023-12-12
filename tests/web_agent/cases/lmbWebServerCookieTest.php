<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_agent\cases;

use limb\web_agent\src\lmbWebServerCookie;
use PHPUnit\Framework\TestCase;

/**
 * @package web_agent
 * @version $Id: lmbWebServerCookieTest.php 39 2007-10-03 21:08:36Z
 */
class lmbWebServerCookieTest extends TestCase
{

    function testCookie()
    {
        $cookie = new lmbWebServerCookie('sid=xzxsd; expires=date; path=/; domain=.google.com; secure');

        $this->assertEquals('sid', $cookie->name);
        $this->assertEquals('xzxsd', $cookie->value);
        $this->assertEquals('date', $cookie->expires);
        $this->assertEquals('/', $cookie->path);
        $this->assertEquals('.google.com', $cookie->domain);
        $this->assertTrue($cookie->secure);
    }

}
