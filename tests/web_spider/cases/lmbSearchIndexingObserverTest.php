<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\net\src\lmbUri;
use limb\web_spider\src\lmbSearchIndexingObserver;
use limb\web_spider\src\lmbUriContentReader;

class TestingSpiderIndexer
{
  function index($uri, $content){}
}

Mock :: generate('lmbUriContentReader', 'MockUriContentReader');
Mock :: generate('TestingSpiderIndexer', 'MockSearchIndexer');

class lmbSearchIndexingObserverTest extends TestCase
{
  var $observer;
  var $indexer;
  var $reader;

  function testNotify()
  {
    $reader = new MockUriContentReader();
    $reader->expectOnce('getUri');
    $reader->setReturnValue('getUri', $uri = new lmbUri('page.html'));

    $reader->expectOnce('getContent');
    $reader->setReturnValue('getContent', $content = 'whatever');

    $indexer = new MockSearchIndexer();
    $indexer->expectOnce('index', array($uri, $content));

    $observer = new lmbSearchIndexingObserver($indexer);
    $observer->notify($reader);
  }
}


