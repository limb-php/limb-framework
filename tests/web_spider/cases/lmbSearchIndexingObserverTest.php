<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_spider\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\web_spider\src\lmbSearchIndexingObserver;
use limb\web_spider\src\lmbUriContentReader;
use tests\web_spider\src\TestingSpiderIndexer;

class lmbSearchIndexingObserverTest extends TestCase
{
    var $observer;
    var $indexer;
    var $reader;

    function testNotify()
    {
        $reader = $this->createMock(lmbUriContentReader::class);
        $reader
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri = new lmbUri('page.html'));

        $reader
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($content = 'whatever');

        $indexer = $this->createMock(TestingSpiderIndexer::class);
        $indexer
            ->expects($this->once())
            ->method('index')
            ->with($uri, $content);

        $observer = new lmbSearchIndexingObserver($indexer);
        $observer->notify($reader);
    }
}
