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
use limb\net\src\lmbHttpRedirectStrategy;
use limb\net\src\lmbHttpResponse;

/*Mock :: generatePartial(
  lmbHttpResponse::class,
  'SpecialMockResponse',
  array('_sendHeader',
        '_sendCookie',
        '_sendString',
        '_sendFile')
);*/

class lmbHttpResponseTest extends TestCase
{
  var $response;

  function setUp(): void
  {
    $this->response = new lmbHttpResponse();

    $this->mock_response = $this->createMock(lmbHttpResponse::class);
  }

  function testIsEmpty()
  {
    $this->assertTrue($this->response->isEmpty());
  }

  function testIsEmptyHeadersSent()
  {
    $this->response->addHeader('test');
    $this->assertTrue($this->response->isEmpty());
  }

  function testNotEmptyRedirect()
  {
    $this->response->redirect("/to/some/place?t=1&amp;t=2");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmptyResponseString()
  {
    $this->response->write("<b>wow</b>");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmptyReadfile()
  {
    $this->response->readfile("/path/to/file");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmpty304Status()
  {
    $this->response->addHeader('HTTP/1.0 304 Not Modified');
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmpty412Status()
  {
    $this->response->addHeader('HTTP/1.1 412 Precondition Failed');
    $this->assertFalse($this->response->isEmpty());
  }

  function testHeadersNotSent()
  {
    $this->assertFalse($this->response->isHeadersSent());
  }

  function testFileNotSent()
  {
    $this->assertFalse($this->response->isFileSent());
  }

  function testFileSent()
  {
    $this->response->readfile('somefile');
    $this->assertTrue($this->response->isFileSent());
  }

  function testHeadersSent()
  {
    $this->response->addHeader("Location:to-some-place");
    $this->assertTrue($this->response->isHeadersSent());
  }

  function testRedirect()
  {
    $this->assertFalse($this->response->isRedirected());

    $this->response->redirect($path = 'some path');

    $this->assertTrue($this->response->isRedirected());
    $this->assertEquals($this->response->getRedirectedPath(), $path);
  }

  function testRedirectOnlyOnce()
  {
    $strategy = $this->createMock(lmbHttpRedirectStrategy::class);

    $this->response->setRedirectStrategy($strategy);

    $this->assertFalse($this->response->isRedirected());

    $strategy->expects($this->once())->method('redirect');

    $this->response->redirect($path = 'some path');
    $this->response->redirect('some other path');

    $this->assertTrue($this->response->isRedirected());
    $this->assertEquals($this->response->getRedirectedPath(), $path);
  }

  /*function testSendHeadersOnCommit()
  {
    $this->mock_response->addHeader("Location:to-some-place");
    $this->mock_response->addHeader("Location:to-some-place2");

    //$this->response->expectCallCount('_sendHeader', 2);

    $this->mock_response
        ->expects($this->at(0))
        ->method('_sendCookie')
        ->with("Location:to-some-place");

    $this->mock_response
        ->expects($this->at(1))
        ->method('_sendCookie')
        ->with("Location:to-some-place2");

    $this->mock_response->commit();
  }

  function testWriteOnCommit()
  {
    $this->mock_response->write("<b>wow</b>");
    $this->mock_response
        ->expects($this->once())
        ->method('_sendString')
        ->with("<b>wow</b>");

    $this->mock_response->commit();
  }

  function testReadfileOnCommit()
  {
    $this->mock_response->readfile("/path/to/file");
    $this->mock_response
        ->expects($this->once())
        ->method('_sendFile')
        ->with("/path/to/file");

    $this->mock_response->commit();
  }*/

  /*function testSendCookiesOnCommit()
  {
    $this->mock_response->setCookie($name1 = 'foo', $value1 = '1', $expire1 = 10, $path1 = '/', $domain1 = '.org', $secure1 = true);
    $this->mock_response->setCookie($name2 = 'bar', $value2 = '2', $expire2 = 20, $path2 = '/path', $domain2 = 'net.org', $secure2 = false);

    //$this->response->expectCallCount('_sendCookie', 2);

    $this->mock_response
        ->expects($this->at(0))
        ->method('_sendCookie')
        ->with(
            array(
                'name' => $name1,
                'value' => $value1,
                'expire' => $expire1,
                'path' => $path1,
                'domain' => $domain1,
                'secure' => $secure1
            )
        );

      $this->mock_response
          ->expects($this->at(1))
          ->method('_sendCookie')
          ->with(
              array(
                  'name' => $name2,
                  'value' => $value2,
                  'expire' => $expire2,
                  'path' => $path2,
                  'domain' => $domain2,
                  'secure' => $secure2
              )
          );

    $this->mock_response->commit();
  }*/

  function testGetResponseDefaultStatus()
  {
    $this->assertEquals(200, $this->response->getStatus());
  }

  function testGetResponseStatusHttp()
  {
    $this->response->addHeader('HTTP/1.0  304 ');
    $this->assertEquals(304, $this->response->getStatus());

    $this->response->addHeader('HTTP/1.1  412');
    $this->assertEquals(412, $this->response->getStatus());
  }

  function testGetUnknownDirective()
  {
    $this->assertFalse($this->response->getDirective('cache-control'));
  }

  function testGetDirective()
  {
    $this->response->addHeader('Cache-Control: protected, max-age=0, must-revalidate');
    $this->assertEquals('protected, max-age=0, must-revalidate', $this->response->getDirective('cache-control'));

    $this->response->addHeader('Cache-Control :    protected, max-age=10  ');
    $this->assertEquals('protected, max-age=10', $this->response->getDirective('cache-control'));
  }

  function testGetContentDefaultType()
  {
    $this->assertEquals('text/html', $this->response->getContentType());
  }

  function testGetContentType()
  {
    $this->response->addHeader('Content-Type', 'image/png');
    $this->assertEquals('image/png', $this->response->getContentType());

    $this->response->addHeader('Content-Type', 'application/rss+xml');
    $this->assertEquals('application/rss+xml', $this->response->getContentType());
  }

  function testGetContentTypeWithDelimiter()
  {
    $this->response->addHeader('Content-Type: text/html; charset=UTF-8');
    $this->assertEquals('text/html', $this->response->getContentType());
  }

  function testAddHeader()
  {
      $this->response->addHeader('Content-Type: text/html');
      $this->assertEquals('text/html', $this->response->getContentType());

      $this->response->addHeader('Content-Type', 'image/png');
      $this->assertEquals('image/png', $this->response->getContentType());
  }
}
