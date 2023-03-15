<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\search\cases\indexer;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\dbal\src\lmbSimpleDb;
use limb\search\src\indexer\lmbFullTextSearchIndexer;
use limb\search\src\indexer\lmbSearchTextNormalizer;
use limb\toolkit\src\lmbToolkit;

require_once (dirname(__FILE__) . '/../.setup.php');

class lmbFullTextSearchIndexerTest extends TestCase
{
  protected $db;

  function setUp(): void
  {
    $conn = lmbToolkit::instance()->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($conn);

    $this->_cleanUp();
  }

  function tearDown(): void
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete(lmb_env_get('FULL_TEXT_SEARCH_INDEXER_TABLE'));
  }

  function testNormalizeContent()
  {
    $uri = new lmbUri('index.html');
    $content = 'content';
    $processed_content = 'new content';

    $normalizer = $this->createMock(lmbSearchTextNormalizer::class);
    $indexer = new lmbFullTextSearchIndexer($normalizer);

    $normalizer
        ->expects($this->once())
        ->method('process')
        ->with($content);
    $normalizer
        ->method('process')
        ->willReturn($processed_content)
        ->with($content);

    $indexer->index($uri, $content);

    $rs = $this->db->select(lmb_env_get('FULL_TEXT_SEARCH_INDEXER_TABLE'));
    $arr = $rs->getArray();

    $this->assertEquals($arr[0]['content'], $processed_content);
  }

  function testNOINDEX()
  {
    $uri = new lmbUri('index.html');

    $content = "must be indexed\n<!-- no index start -->ignored by indexer<!-- no index end -->must be indexed also";

    $expected = "must be indexed\n must be indexed also";

    $normalizer = $this->createMock(lmbSearchTextNormalizer::class);
    $indexer = new lmbFullTextSearchIndexer($normalizer);
    $indexer->useNOINDEX();

    $normalizer
        ->expects($this->once())
        ->method('process')
        ->with($expected);
    $normalizer
        ->method('process')
        ->willReturn('whatever');

    $indexer->index($uri, $content);
  }

  function testSwitchOffNOINDEX()
  {
    $uri = new lmbUri('index.html');

    $content = "must be indexed\n<!-- no index start -->ignored by indexer<!-- no index end -->must be indexed also";

    $expected = "must be indexed\n<!-- no index start -->ignored by indexer<!-- no index end -->must be indexed also";

    $normalizer = $this->createMock(lmbSearchTextNormalizer::class);
    $indexer = new lmbFullTextSearchIndexer($normalizer);
    $indexer->useNOINDEX(false);

    $normalizer
        ->expects($this->once())
        ->method('process')
        ->with($expected);
    $normalizer
        ->method('process')
        ->willReturn('whatever')
        ->with($expected);
    $indexer->index($uri, $content);
  }

  function testNOINDEXMultiline()
  {
    $uri = new lmbUri('index.html');

    $content = "\nmust be indexed\n\n<!-- no index start -->ignored by indexer\n<!-- no index end -->\n must be indexed also";

    $expected = "\nmust be indexed\n\n \n must be indexed also";

    $normalizer = $this->createMock(lmbSearchTextNormalizer::class);
    $indexer = new lmbFullTextSearchIndexer($normalizer);
    $indexer->useNOINDEX();

    $normalizer
        ->expects($this->once())
        ->method('process')
        ->with($expected);
    $normalizer
        ->method('process')
        ->willReturn('whatever');

    $indexer->index($uri, $content);
  }

  function testIndexNew()
  {
    $uri = new lmbUri('index.html');
    $content = '<title>test title</title>content';
    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri, $content);

    $rs = $this->db->select(lmb_env_get('FULL_TEXT_SEARCH_INDEXER_TABLE'));
    $arr = $rs->getArray();

    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['uri'], $uri->toString());
    $this->assertEquals('test title content', $arr[0]['content']);
    $this->assertEquals('test title', $arr[0]['title']);
    $this->assertTrue($arr[0]['last_modified'] > 0 && $arr[0]['last_modified'] <= time());
  }

  function testIndexNewUtf8Text()
  {
    $uri = new lmbUri('index.html');
    $content = '<title>Plants</title>Delivery';
    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri, $content);

    $rs = $this->db->select(lmb_env_get('FULL_TEXT_SEARCH_INDEXER_TABLE'));
    $arr = $rs->getArray();

    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['uri'], $uri->toString());
    $this->assertEquals('plants delivery', $arr[0]['content']);
    $this->assertEquals('Plants', $arr[0]['title']);
    $this->assertTrue($arr[0]['last_modified'] > 0 && $arr[0]['last_modified'] <= time());
  }

  function testExtractTitleAnyway()
  {
    $uri = new lmbUri('index.html');
    $content = '<!-- no index start --><title>test title</title>content ignored<!-- no index end -->content';

    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->useNOINDEX();
    $indexer->index($uri, $content);

    $rs = $this->db->select(lmb_env_get('FULL_TEXT_SEARCH_INDEXER_TABLE'));
    $arr = $rs->getArray();

    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['uri'], $uri->toString());
    $this->assertEquals('content', $arr[0]['content']);
    $this->assertEquals('test title', $arr[0]['title']);
    $this->assertTrue($arr[0]['last_modified'] > 0 && $arr[0]['last_modified'] <= time());
  }

  function testIndexUpdate()
  {
    $uri = new lmbUri('index.html');

    $this->db->insert(lmb_env_get('FULL_TEXT_SEARCH_INDEXER_TABLE'),
                          array('uri' => $uri->toString(),
                                'content' => 'content1',
                                'title' => 'title1',
                                'last_modified' => $time1 = 200));

    $new_content = '<title>title2</title>content2';

    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri, $new_content);

    $rs = $this->db->select(lmb_env_get('FULL_TEXT_SEARCH_INDEXER_TABLE'));
    $arr = $rs->getArray();

    $this->assertEquals(1, sizeof($arr));
    $this->assertEquals($arr[0]['uri'], $uri->toString());
    $this->assertEquals('title2 content2', $arr[0]['content']);
    $this->assertEquals('title2', $arr[0]['title']);
    $this->assertTrue($arr[0]['last_modified'] > $time1 && $arr[0]['last_modified'] <= time());
  }

  function testFindIndexRecordByUri()
  {
    $uri1 = new lmbUri('index.html');
    $content1 = '<title>title1</title>content1';

    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri1, $content1);

    $uri2 = new lmbUri('page1.html');
    $content2 = '<title>title2</title>content2';

    $indexer->index($uri2, $content2);

    $record = $indexer->findIndexRecordByUri($uri1)->export();
    $this->assertEquals($record['uri'], $uri1->toString());
    $this->assertEquals('title1 content1', $record['content']);
    $this->assertEquals('title1', $record['title']);

    $record = $indexer->findIndexRecordByUri($uri2)->export();
    $this->assertEquals($record['uri'], $uri2->toString());
    $this->assertEquals('title2 content2', $record['content']);
    $this->assertEquals('title2', $record['title']);
  }
}
