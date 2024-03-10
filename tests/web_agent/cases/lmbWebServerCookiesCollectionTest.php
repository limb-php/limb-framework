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
use limb\web_agent\src\lmbWebServerCookiesCollection;
use PHPUnit\Framework\TestCase;

/**
 * @package web_agent
 * @version $Id: lmbWebServerCookiesCollectionTest.php 43 2007-10-05 15:33:11Z
 */
class lmbWebServerCookiesCollectionTest extends TestCase
{

    function testAdd()
    {
        $collect = new lmbWebServerCookiesCollection();
        $cookies = array(
            new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.google.com; secure'),
            new lmbWebServerCookie('sid=sid2; expires=date; path=/; domain=.google.com; secure'),
            new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.google.com; secure'),
            new lmbWebServerCookie('sid2=sid2; expires=date; path=/; domain=.google.com; secure')
        );
        foreach ($cookies as $cookie) {
            $collect->add($cookie);
        }
        $it = $collect->getIterator();
        $it->rewind();
        $cookie = $it->current();
        $this->assertEquals($cookies[1], $cookie);
        $it->next();
        $cookie = $it->current();
        $this->assertEquals($cookies[2], $cookie);
        $it->next();
        $cookie = $it->current();
        $this->assertEquals($cookies[3], $cookie);
    }

    function testSearchCookie()
    {
        $collect = new lmbWebServerCookiesCollection();
        $cookies = array(
            new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.google.com; secure'),
            new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.google.com; secure'),
            new lmbWebServerCookie('sid2=sid2; expires=date; path=/; domain=.google.com; secure')
        );
        foreach ($cookies as $cookie) {
            $collect->add($cookie);
        }

        $this->assertEquals(1, $collect->search('sid', '/sub', '.google.com'));
        $this->assertFalse($collect->search('sid', '/sub', '.google2.com'));
        $this->assertFalse($collect->search('sid1'));
    }

    function testGet()
    {
        $collect = new lmbWebServerCookiesCollection();
        $cookies = array(
            new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.google.com; secure'),
            new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.google.com; secure'),
            new lmbWebServerCookie('sid2=sid3; expires=date; path=/; domain=.google.com; secure')
        );
        foreach ($cookies as $cookie) {
            $collect->add($cookie);
        }

        $this->assertEquals('sid2', $collect->get(1)->value);
        $this->assertFalse($collect->get(3));
    }

    function testCopyTo()
    {
        $collect = new lmbWebServerCookiesCollection();
        $cookies = array(
            new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.google.com; secure'),
            new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.google.com; secure'),
            new lmbWebServerCookie('sid2=sid2; expires=date; path=/; domain=.google.com; secure')
        );
        foreach ($cookies as $cookie) {
            $collect->add($cookie);
        }
        $collect2 = new lmbWebServerCookiesCollection();
        $cookies2 = array(
            new lmbWebServerCookie('sid=sid2; expires=date; path=/; domain=.google.com; secure'),
        );
        foreach ($cookies2 as $cookie) {
            $collect2->add($cookie);
        }

        $collect->copyTo($collect2);
        $it = $collect2->getIterator();
        $it->rewind();
        $cookie = $it->current();
        $this->assertEquals($cookies[0], $cookie);
        $this->assertEquals('sid', $cookie->name);
        $this->assertEquals('sid1', $cookie->value);
        $this->assertEquals('/', $cookie->path);
        $it->next();
        $cookie = $it->current();
        $this->assertEquals($cookies[1], $cookie);
        $this->assertEquals('sid', $cookie->name);
        $this->assertEquals('sid2', $cookie->value);
        $this->assertEquals('/sub', $cookie->path);
        $it->next();
        $cookie = $it->current();
        $this->assertEquals($cookies[2], $cookie);
        $this->assertEquals('sid2', $cookie->name);
        $this->assertEquals('sid2', $cookie->value);
        $this->assertEquals('/', $cookie->path);
    }

}
