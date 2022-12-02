<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

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
    $reader->expectOnce('getUri');
    $reader->setReturnValue('getUri', $uri = new lmbUri('page.html'));

    $reader->expectOnce('getContent');
    $reader->setReturnValue('getContent', $content = 'whatever');

    $indexer = $this->createMock(TestingSpiderIndexer::class);
    $indexer->expectOnce('index', array($uri, $content));

    $observer = new lmbSearchIndexingObserver($indexer);
    $observer->notify($reader);
  }
}
