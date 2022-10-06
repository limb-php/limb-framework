<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\web_agent\src\lmbWebServerCookiesCollection;

/**
 * @package web_agent
 * @version $Id: lmbWebServerCookiesCollectionTest.class.php 43 2007-10-05 15:33:11Z CatMan $
 */
class lmbWebServerCookiesCollectionTest extends TestCase {

  function testAdd()
  {
    $collect = new lmbWebServerCookiesCollection();
    $cookies = array(
      new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid=sid2; expires=date; path=/; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid2=sid2; expires=date; path=/; domain=.test.ru; secure')
    );
    foreach($cookies as $cookie)
    {
      $collect->add($cookie);
    }
    $it = $collect->getIterator();
    $it->rewind();
    $cookie = $it->current();
    $this->assertIdentical($cookies[1], $cookie);
    $it->next();
    $cookie = $it->current();
    $this->assertIdentical($cookies[2], $cookie);
    $it->next();
    $cookie = $it->current();
    $this->assertIdentical($cookies[3], $cookie);
  }

  function testSearchCookie()
  {
    $collect = new lmbWebServerCookiesCollection();
    $cookies = array(
      new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid2=sid2; expires=date; path=/; domain=.test.ru; secure')
    );
    foreach($cookies as $cookie)
    {
      $collect->add($cookie);
    }

    $this->assertEquals($collect->search('sid', '/sub', '.test.ru'), 1);
    $this->assertFalse($collect->search('sid', '/sub', '.test1.ru'));
    $this->assertFalse($collect->search('sid1'));
  }

  function testGet()
  {
    $collect = new lmbWebServerCookiesCollection();
    $cookies = array(
      new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid2=sid3; expires=date; path=/; domain=.test.ru; secure')
    );
    foreach($cookies as $cookie)
    {
      $collect->add($cookie);
    }

    $this->assertEquals($collect->get(1)->value, 'sid2');
    $this->assertFalse($collect->get(3));
  }

  function testCopyTo()
  {
    $collect = new lmbWebServerCookiesCollection();
    $cookies = array(
      new lmbWebServerCookie('sid=sid1; expires=date; path=/; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid=sid2; expires=date; path=/sub; domain=.test.ru; secure'),
      new lmbWebServerCookie('sid2=sid2; expires=date; path=/; domain=.test.ru; secure')
    );
    foreach($cookies as $cookie)
    {
      $collect->add($cookie);
    }
    $collect2 = new lmbWebServerCookiesCollection();
    $cookies2 = array(
      new lmbWebServerCookie('sid=sid2; expires=date; path=/; domain=.test.ru; secure'),
    );
    foreach($cookies2 as $cookie)
    {
      $collect2->add($cookie);
    }

    $collect->copyTo($collect2);
    $it = $collect2->getIterator();
    $it->rewind();
    $cookie = $it->current();
    $this->assertClone($cookies[0], $cookie);
    $this->assertEquals($cookie->name, 'sid');
    $this->assertEquals($cookie->value, 'sid1');
    $this->assertEquals($cookie->path, '/');
    $it->next();
    $cookie = $it->current();
    $this->assertClone($cookies[1], $cookie);
    $this->assertEquals($cookie->name, 'sid');
    $this->assertEquals($cookie->value, 'sid2');
    $this->assertEquals($cookie->path, '/sub');
    $it->next();
    $cookie = $it->current();
    $this->assertClone($cookies[2], $cookie);
    $this->assertEquals($cookie->name, 'sid2');
    $this->assertEquals($cookie->value, 'sid2');
    $this->assertEquals($cookie->path, '/');
  }

}
