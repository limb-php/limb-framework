<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_spider\cases;

use limb\web_spider\src\lmbMetaFilter;
use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\web_spider\src\lmbWebSpider;
use limb\web_spider\src\lmbContentTypeFilter;
use limb\web_spider\src\lmbUriFilter;
use limb\web_spider\src\lmbUriExtractor;
use limb\web_spider\src\lmbUriNormalizer;
use limb\web_spider\src\lmbUriContentReader;
use tests\web_spider\src\TestingSpiderObserver;

class lmbWebSpiderTest extends TestCase
{
    protected $spider;
    protected $observer;
    protected $extractor;
    protected $uri_filter;
    protected $content_type_filter;
    protected $normalizer;
    protected $reader;

    function setUp(): void
    {
        parent::setUp();

        $this->observer = $this->createMock(TestingSpiderObserver::class);
        $this->extractor = $this->createMock(lmbUriExtractor::class);
        $this->uri_filter = $this->createMock(lmbUriFilter::class);
        $this->content_type_filter = $this->createMock(lmbContentTypeFilter::class);
        $this->normalizer = $this->createMock(lmbUriNormalizer::class);
        $this->reader = $this->createMock(lmbUriContentReader::class);
        $this->meta_filter = $this->createMock(lmbMetaFilter::class);

        $this->spider = new lmbWebSpider();
        $this->spider->registerObserver($this->observer);
        $this->spider->setUriExtractor($this->extractor);
        $this->spider->setUriFilter($this->uri_filter);
        $this->spider->setContentTypeFilter($this->content_type_filter);
        $this->spider->setUriNormalizer($this->normalizer);
        $this->spider->setMetaFilter($this->meta_filter);
        $this->spider->setUriContentReader($this->reader);
    }

    function tearDown(): void
    {
        parent::tearDown();
    }

    function testContentTypeFiltering()
    {
        $uri = new lmbUri('https://some.host/whatever.html');

        $this->uri_filter
            ->expects($this->once())
            ->method('canPass')
            ->with($uri)
            ->willReturn(true);

        $this->normalizer
            ->expects($this->once())
            ->method('process')
            ->with($uri)
            ->willReturn($uri);

        $this->reader
            ->expects($this->once())
            ->method('open')
            ->with($uri);

        $this->reader
            ->method('getContentType')
            ->willReturn($content_type = 'whatever');

        $this->reader
            ->expects($this->never())
            ->method('getContent');

        $this->content_type_filter
            ->expects($this->once())
            ->method('canPass')
            ->with($content_type)
            ->willReturn(false);

        $this->extractor
            ->expects($this->never())
            ->method('extract');

        $this->observer
            ->expects($this->never())
            ->method('notify');

        $this->spider->crawl($uri);
    }

    function testNotifyObservers()
    {
        $one_more_observer = $this->createMock(TestingSpiderObserver::class);
        $this->spider->registerObserver($one_more_observer);

        $uri = new lmbUri('https://some.host/whatever.html');

        $this->uri_filter
            ->expects($this->once())
            ->method('canPass')
            ->with($uri)
            ->willReturn(true);

        $this->normalizer
            ->expects($this->once())
            ->method('process')
            ->with($uri)
            ->willReturn($uri);

        $this->reader
            ->expects($this->once())
            ->method('open')
            ->with($uri);

        $this->reader
            ->method('getContent')
            ->willReturn($content = 'whatever');

        $this->reader
            ->method('getContentType')
            ->willReturn($content_type = 'whatever');

        $this->content_type_filter
            ->expects($this->once())
            ->method('canPass')
            ->with($content_type)
            ->willReturn(true);

        $this->extractor
            ->expects($this->once())
            ->method('extract')
            ->with($content)
            ->willReturn(array());

        $this->observer
            ->expects($this->once())
            ->method('notify')
            ->with($this->createMock(lmbUriContentReader::class));

//        $this->observer
//            ->expects($this->once())
//            ->method('canPass')
//            ->with('<html></html>')
//            ->willReturn(true);

        $one_more_observer
            ->expects($this->once())
            ->method('notify')
            ->with($this->createMock(lmbUriContentReader::class));

        $this->spider->crawl($uri);
    }

    function testCrawlCacheHitComplexUrl()
    {
        $this->observer
            ->expects($this->exactly(2))
            ->method('notify');

        $uri = new lmbUri('https://example.com/index.html');
        $uri_normalized_by_spider = new lmbUri('https://example.com/level1/page1.html');

        $this->uri_filter
            ->expects($this->exactly(2))
            ->method('canPass')
            ->willReturnMap([
                [$uri], [$uri_normalized_by_spider]
            ])
            ->willReturnOnConsecutiveCalls(true, true);

        $this->normalizer
            ->expects($this->exactly(5))
            ->method('process')
            ->willReturnMap([
                [$uri], [$uri], [$uri_normalized_by_spider], [$uri], [$uri_normalized_by_spider]
            ]) //???
            ->willReturnOnConsecutiveCalls($uri, $uri, $uri_normalized_by_spider, $uri, $uri_normalized_by_spider);

        $this->reader
            ->expects($this->exactly(2))
            ->method('open')
            ->willReturnOnConsecutiveCalls($uri, $uri_normalized_by_spider);

        $content1 = 'whatever1';
        $content2 = 'whatever2';
        $this->reader
            ->expects($this->exactly(1))
            ->method('getContent')
            ->willReturnOnConsecutiveCalls('whatever1', 'whatever2');

        $this->reader
            ->expects($this->exactly(1))
            ->method('getContentType')
            ->willReturnOnConsecutiveCalls($content_type1 = 'type1', $content_type2 = 'type2');

        $this->content_type_filter
            ->expects($this->exactly(2))
            ->method('canPass')
            ->willReturnMap([
                [$content_type1], [$content_type2]
            ])
            ->willReturnOnConsecutiveCalls(true, true);

        $links1 = array(new lmbUri('index.html'), new lmbUri('level1/page1.html#anchor'));
        $links2 = array(new lmbUri('../index.html'), new lmbUri('page1.html'));

        $this->extractor
            ->expects($this->exactly(2))
            ->method('extract')
            ->willReturnMap([
                [$links1], [$links2]
            ])
            ->willReturnOnConsecutiveCalls([$links1, $links2], [$links2]);

        $this->spider->crawl($uri);
    }
}
