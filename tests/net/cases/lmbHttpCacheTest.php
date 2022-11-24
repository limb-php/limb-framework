<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\net\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbHttpResponse;
use limb\net\src\lmbHttpCache;

class lmbHttpCacheTest extends TestCase
{
  var $response;
  var $cache;
  var $server_vars;

  function setUp(): void
  {
    $this->response = $this->createMock(lmbHttpResponse::class);
    $this->cache = new lmbHttpCache();
    $this->server_vars = $_SERVER;
  }

  function tearDown(): void
  {
    $_SERVER = $this->server_vars;
  }

  function testSetCacheSettings()
  {
    $this->cache->setLastModifiedTime($time = time());
    $this->assertEquals($this->cache->getLastModifiedTime(), $time);
    $this->assertEquals($this->cache->formatLastModifiedTime(), gmdate('D, d M Y H:i:s \G\M\T', $time));

    $this->cache->setEtag($etag = md5(time()));
    $this->assertEquals($this->cache->getEtag(), $etag);

    $this->cache->setCacheTime(10);
    $this->assertEquals($this->cache->getCacheTime(), 10);

    $this->cache->setCacheType('public');
    $this->assertEquals($this->cache->getCacheType(), 'public');
  }

  function testGetDefaultEtag1()
  {
    $script = 'test';
    $query = 'query';

    $_SERVER['QUERY_STRING'] = $query;
    $_SERVER['SCRIPT_FILENAME'] = $script;

    $this->cache->setLastModifiedTime($time = time());
    $etag = $this->cache->getEtag();

    $this->assertEquals($etag, '"' . md5($script . '?' . $query . '#' . $time ) . '"');
  }

  function testGetDefaultEtag2()
  {
    $script = 'test';
    $query = 'query';

    $_SERVER['QUERY_STRING'] = $query;
    unset($_SERVER['SCRIPT_FILENAME']);
    $_SERVER['PATH_TRANSLATED'] = $script;

    $this->cache->setLastModifiedTime($time = time());
    $etag = $this->cache->getEtag();

    $this->assertEquals($etag, '"' . md5($script . '?' . $query . '#' . $time ) . '"');
  }

  function testGetDefaultEtag3()
  {
    $script = 'test';

    unset($_SERVER['QUERY_STRING']);
    $_SERVER['SCRIPT_FILENAME'] = $script;

    $this->cache->setLastModifiedTime($time = time());
    $etag = $this->cache->getEtag();

    $this->assertEquals($etag, '"' . md5($script . '#' . $time ) . '"');
  }

  function testIs412False()
  {
    $this->assertFalse($this->cache->is412());
  }

  function testIs412FalsePartOfEtag()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'big_etag';

    $this->cache->setEtag('etag');

    $this->assertFalse($this->cache->is412());
  }

  function testIs412FalseAsteric()
  {
    $_SERVER['HTTP_IF_MATCH'] = '*';

    $this->cache->setEtag('etag');

    $this->assertFalse($this->cache->is412());
  }

  function testIs412Etag()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'wrong';

    $this->cache->setEtag('etag');

    $this->assertTrue($this->cache->is412());
  }

  function testIs412UnmodifiedSince()
  {
    $this->cache->setLastModifiedTime($time = time());

    $_SERVER['HTTP_IF_UNMODIFIED_SINCE'] = gmdate('D, d M Y H:i:s \G\M\T', $time - 100);

    $this->assertTrue($this->cache->is412());
  }

  function testIs304False()
  {
    $this->assertFalse($this->cache->is304());
  }

  function testIs304LastModifiedTime()
  {
    $this->cache->setLastModifiedTime($time = time());

    $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $this->cache->formatLastModifiedTime();

    $this->assertTrue($this->cache->is304());
  }

  function testIs304Etag()
  {
    $etag = 'etag';

    unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $_SERVER['HTTP_IF_NONE_MATCH'] = $etag;

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setEtag($etag);

    $this->assertTrue($this->cache->is304());
  }

  function testIs304EtagAsteric()
  {
    $etag = 'etag';

    unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $_SERVER['HTTP_IF_NONE_MATCH'] = '*';

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setEtag($etag);

    $this->assertTrue($this->cache->is304());
  }

  function testCheckAndWrite412()
  {
    $_SERVER['HTTP_IF_MATCH'] = 'wrong';

    $this->cache->setEtag('etag');

    $this->response
        ->expects($this->exactly(3))
        ->method('addHeader');


    $this->response
        ->method('addHeader')
        ->withConsecutive(
            ['HTTP/1.1 412 Precondition Failed'],
            ['Cache-Control: protected, max-age=0, must-revalidate'],
            ['Content-Type: text/plain']
        );

    /*$this->response
        ->expects($this->once())
        ->method('write')
        ->with(new WantedPatternExpectation("~^HTTP/1.1 Error 412~"));*/

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWrite304()
  {
    $_SERVER['HTTP_IF_NONE_MATCH'] = 'etag';

    $this->cache->setEtag('etag');

    $this->response
        ->expects($this->exactly(6))
        ->method('addHeader');

    $this->response
        ->method('addHeader')
        ->withConsecutive(
            ['HTTP/1.0 304 Not Modified'],
            ['Etag: etag'],
            ['Pragma: '],
            ['Cache-Control: '],
            ['Last-Modified: '],
            ['Expires: ']
        );

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteFalseNotHead()
  {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->assertFalse($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteNoCacheTime()
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';

    $this->cache->setLastModifiedTime($time = time());

    $this->response
        ->expects($this->exactly(5))
        ->method('addHeader');

    $this->response
        ->method('addHeader')
        ->withConsecutive(
            ['Cache-Control: protected, must-revalidate, max-age=0'],
            ['Last-Modified: ' . $this->cache->formatLastModifiedTime()],
            ['Etag: ' . $this->cache->getEtag()],
            ['Pragma: '],
            ['Expires: ']
        );

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteWithCacheTime()
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setCacheTime(100);

    $this->response
      ->expects($this->exactly(5))
      ->method('addHeader');

    $this->response
        ->method('addHeader')
        ->withConsecutive(
            ['Cache-Control: protected, max-age=100'],
            ['Last-Modified: ' . $this->cache->formatLastModifiedTime()],
            ['Etag: ' . $this->cache->getEtag()],
            ['Pragma: '],
            ['Expires: ']
        );

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }

  function testCheckAndWriteWithPrivacy()
  {
    $_SERVER['REQUEST_METHOD'] = 'HEAD';

    $this->cache->setLastModifiedTime($time = time());
    $this->cache->setCacheTime(100);
    $this->cache->setCacheType(lmbHttpCache::TYPE_PUBLIC);

    $this->response
        ->expects($this->exactly(5))
        ->method('addHeader');

    $this->response
        ->method('addHeader')
        ->withConsecutive(
            ['Cache-Control: public, max-age=100'],
            ['Last-Modified: ' . $this->cache->formatLastModifiedTime()],
            ['Etag: ' . $this->cache->getEtag()],
            ['Pragma: '],
            ['Expires: ']
        );

    $this->assertTrue($this->cache->checkAndWrite($this->response));
  }
}
