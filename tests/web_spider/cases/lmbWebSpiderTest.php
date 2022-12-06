<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\web_spider\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\web_spider\src\lmbWebSpider;
use limb\web_spider\src\lmbContentTypeFilter;
use limb\web_spider\src\lmbUriFilter;
use limb\web_spider\src\lmbUriExtractor;
use limb\web_spider\src\lmbUriNormalizer;
use limb\web_spider\src\lmbUriContentReader;

class TestingSpiderObserver
{
  function notify($reader){}
}

class lmbWebSpiderTest extends TestCase
{
  var $spider;
  var $observer;
  var $extractor;
  var $uri_filter;
  var $content_type_filter;
  var $normalizer;
  var $reader;

  function setUp(): void
  {
    $this->observer = $this->createMock(TestingSpiderObserver::class);
    $this->extractor = $this->createMock(lmbUriExtractor::class);
    $this->uri_filter = $this->createMock(lmbUriFilter::class);
    $this->content_type_filter = $this->createMock(lmbContentTypeFilter::class);
    $this->normalizer = $this->createMock(lmbUriNormalizer::class);
    $this->reader = $this->createMock(lmbUriContentReader::class);

    $this->spider = new lmbWebSpider();
    $this->spider->registerObserver($this->observer);
    $this->spider->setUriExtractor($this->extractor);
    $this->spider->setUriFilter($this->uri_filter);
    $this->spider->setContentTypeFilter($this->content_type_filter);
    $this->spider->setUriNormalizer($this->normalizer);
    $this->spider->setUriContentReader($this->reader);
  }

  function tearDown(): void
  {
  }

  function testContentTypeFiltering()
  {
    $uri = new lmbUri('http://some.host/whatever.html');

    $this->uri_filter
        ->expects($this->once())
        ->method('canPass')
        ->with($uri)
        ->willReturn(true);

    $this->normalizer
        ->expects($this->once())
        ->method('process');

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

    $uri = new lmbUri('http://some.host/whatever.html');

    $this->uri_filter
        ->expects($this->once())
        ->method('canPass')
        ->with($uri)
        ->willReturn(true);

    $this->normalizer
        ->expects($this->once())
        ->method('process');

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
        ->with( $this->createMock(lmbUriContentReader::class) );
    $one_more_observer
        ->expects($this->once())
        ->method('notify')
        ->with( $this->createMock(lmbUriContentReader::class) );

    $this->spider->crawl($uri);
  }

  function testCrawlCacheHitComplexUrl()
  {
    $this->observer
        ->expects($this->exactly(2))
        ->method('notify');

    $uri = new lmbUri('http://example.com/index.html');
    $uri_normalized_by_spider = new lmbUri('http://example.com/level1/page1.html');

    $this->uri_filter
        ->expectCallCount('canPass', 2);
    $this->uri_filter
        ->setReturnValueAt(0, 'canPass', true, array($uri));
    $this->uri_filter
        ->setReturnValueAt(1, 'canPass', true, array($uri_normalized_by_spider));

    $this->normalizer
        ->expectCallCount('process', 5);
    $this->normalizer
        ->expectArgumentsAt(0, 'process', array($uri));
    $this->normalizer
        ->expectArgumentsAt(1, 'process', array($uri));
    $this->normalizer
        ->expectArgumentsAt(2, 'process', array($uri_normalized_by_spider));
    $this->normalizer
        ->expectArgumentsAt(3, 'process', array($uri));
    $this->normalizer
        ->expectArgumentsAt(4, 'process', array($uri_normalized_by_spider));

    $this->reader
        ->expectCallCount('open', 2);
    $this->reader
        ->expectArgumentsAt(0, 'open', array($uri));
    $this->reader
        ->expectArgumentsAt(1, 'open', array($uri_normalized_by_spider));

    $this->reader
        ->expectCallCount('getContent', 2);
    $this->reader
        ->setReturnValueAt(0, 'getContent', $content1 = 'whatever1');
    $this->reader
        ->setReturnValueAt(0, 'getContentType', $content_type1 = 'type1');
    $this->reader
        ->setReturnValueAt(1, 'getContent', $content2 = 'whatever2');
    $this->reader
        ->setReturnValueAt(1, 'getContentType', $content_type2 = 'type2');

    $this->content_type_filter
        ->expectCallCount('canPass', 2);
    $this->content_type_filter
        ->setReturnValueAt(0 ,'canPass', true, array($content_type1));
    $this->content_type_filter
        ->setReturnValueAt(1 ,'canPass', true, array($content_type2));

    $links1 = array(new lmbUri('index.html'), new lmbUri('level1/page1.html#anchor'));
    $links2 = array(new lmbUri('../index.html'), new lmbUri('page1.html'));

    $this->extractor
        ->method()
        ->expectCallCount('extract', 2);
    $this->extractor
        ->method()
        ->setReturnValue('extract', $links1, array($content1));
    $this->extractor
        ->method()
        ->setReturnValue('extract', $links2, array($content2));

    $this->spider->crawl($uri);
  }
}
