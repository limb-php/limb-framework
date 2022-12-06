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
use limb\web_spider\src\lmbSearchIndexingObserver;
use limb\web_spider\src\lmbUriContentReader;

class TestingSpiderIndexer
{
  function index($uri, $content){}
}

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
