<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_spider\cases;

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

    function testNotifyInnerUrl()
    {
        $observer = new lmbInnerUriNormalizerObserver(new lmbUri('https://test.com'));
        $this->reader
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri = new lmbUri('https://test.com/page.html'));

        $observer->notify($this->reader);
        $this->assertEquals('/page.html', $uri->toString(['path', 'query', 'anchor']));
    }

    function testNotifyOtherProtocol()
    {
        $observer = new lmbInnerUriNormalizerObserver(new lmbUri('https://test.com'));
        $this->reader
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri = new lmbUri('ftp://test.com/page.html'));

        $observer->notify($this->reader);
        $this->assertEquals('ftp://test.com/page.html', $uri->toString());
    }

    function testNotifyOtherPort()
    {
        $observer = new lmbInnerUriNormalizerObserver(new lmbUri('https://test.com:22'));
        $this->reader
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri = new lmbUri('https://test.com/page.html'));

        $observer->notify($this->reader);
        $this->assertEquals('https://test.com/page.html', $uri->toString());
    }

    function testNotifyExternalUrl()
    {
        $observer = new lmbInnerUriNormalizerObserver(new lmbUri('https://test.com'));
        $this->reader
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri = new lmbUri('https://test2.com/page.html'));

        $observer->notify($this->reader);
        $this->assertEquals('https://test2.com/page.html', $uri->toString());
    }
}
