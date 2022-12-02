<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use PHPUnit\Framework\TestCase;
use limb\web_spider\src\lmbInnerUriNormalizerObserver;
use limb\web_spider\src\lmbUriContentReader;
use limb\net\src\lmbUri;

class lmbInnerUriNormalizerObserverTest extends TestCase
{
  var $observer;
  var $reader;

  function setUp(): void
  {
    $this->reader = $this->createMock(lmbUriContentReader::class);
  }

  function tearDown(): void
  {
    $this->reader->tally();
  }

  function testNotifyInnerUrl()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('http://test.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEquals($uri->toString(), '/page.html');
  }

  function testNotifyOtherProtocol()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('ftp://test.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEquals($uri->toString(), 'ftp://test.com/page.html');
  }

  function testNotifyOtherPort()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com:22'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('http://test.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEquals($uri->toString(), 'http://test.com/page.html');
  }

  function testNotifyExternalUrl()
  {
    $observer = new lmbInnerUriNormalizerObserver(new lmbUri('http://test.com'));
    $this->reader->expectOnce('getUri');
    $this->reader->setReturnReference('getUri', $uri = new lmbUri('http://test2.com/page.html'));

    $observer->notify($this->reader);
    $this->assertEquals($uri->toString(), 'http://test2.com/page.html');
  }

}
