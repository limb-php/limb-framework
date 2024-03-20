<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\net\cases;

use limb\net\src\lmbJsonHttpResponse;
use limb\net\src\lmbMetaRedirectStrategy;
use PHPUnit\Framework\TestCase;
use limb\net\src\lmbHttpRedirectStrategy;
use limb\net\src\lmbHttpResponse;

class lmbHttpResponseTest extends TestCase
{
    protected $response;
    protected $json_response;
    protected $mock_response;

    function setUp(): void
    {
        $this->response = new lmbHttpResponse();
        $this->json_response = new lmbJsonHttpResponse();

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

    function testResponseStringWrite()
    {
        $this->response->write("<b>wow</b>");
        $this->response->write("<b>wow2</b>", true);
        $this->assertEquals('<b>wow2</b>', $this->response->getBody());
    }

    function testResponseStringAppend()
    {
        $this->response->write("<b>wow</b>");
        $this->response->write("<b>wow2</b>");
        $this->response->write("<b>wow3</b>");
        $this->assertEquals('<b>wow</b><b>wow2</b><b>wow3</b>', $this->response->getBody());
    }

    function testNotEmptyReadfile()
    {
        $filename = __DIR__ . "/input.jpg";
        $this->response->readFile($filename);

        $this->assertFalse($this->response->isEmpty());
        $this->assertEquals(file_get_contents($filename), $this->response->getBody());
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

    function testHeadersSent()
    {
        $this->response->addHeader("Location", "to-some-place");
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

        $strategy
            ->expects($this->once())
            ->method('redirect');

        $this->response->redirect($path = 'some path');
        $this->response->redirect('some other path');

        $this->assertTrue($this->response->isRedirected());
        $this->assertEquals($this->response->getRedirectedPath(), $path);
    }

    function testRedirectMetaStrategy()
    {
        $strategy = $this->createMock(lmbMetaRedirectStrategy::class);

        $this->response->setRedirectStrategy($strategy);

        $this->assertFalse($this->response->isRedirected());

        $strategy
            ->expects($this->once())
            ->method('redirect');

        $this->response->redirect($path = 'some path');

        $this->assertTrue($this->response->isRedirected());
        $this->assertEquals($this->response->getRedirectedPath(), $path);
    }

//    function testSendHeadersOnCommit()
//    {
//      $this->mock_response->setCookie('foo', '111');
//      $this->mock_response->addHeader("Location", "to-some-place");
//      $this->mock_response->addHeader("Location", "to-some-place2");
//
//      $this->mock_response
//          ->expects($this->exactly(1))
//          ->method('sendHeaders');
//
//      $this->mock_response->commit();
//    }

    /*function testWriteOnCommit()
    {
      $this->mock_response->write("<b>wow</b>");
      $this->mock_response
          ->expects($this->once())
          ->method('_sendString')
          ->with("<b>wow</b>");

      $this->mock_response->commit();
    }*/

    /*function testReadfileOnCommit()
    {
      $this->mock_response->readFile("/path/to/file");
      $this->mock_response
          ->expects($this->once())
          ->method('_fileToStream')
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

    function testWithBody()
    {
        $this->response = $this->response->withBody($content1 = 'body content !!!');
        $this->response = $this->response->withBody($content2 = 'body content');

        $this->assertEquals($content2, $this->response->getBody());
    }

    function testWithJsonBody()
    {
        $this->json_response = $this->json_response->withBody($content = ['data' => '123']);
        $json_content = json_encode($content);

        $this->assertEquals($json_content, $this->json_response->getBody());
    }
}
