<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\net\cases;

use limb\net\src\lmbHttpStream;
use PHPUnit\Framework\TestCase;
use limb\net\src\lmbHttpRequest;
use limb\net\src\lmbUri;
use limb\net\src\lmbUploadedFile;

class lmbHttpRequestTest extends TestCase
{
    function testRequestExport()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('c' => 1), array('d' => 2));

        $this->assertEquals($request->export(), [
            'c' => 1,
            'd' => 2
        ]);
    }

    function testGetUri()
    {
        $request = new lmbHttpRequest('https://test.com');
        $this->assertEquals($request->getUri(), new lmbUri('https://test.com'));
    }

    function testGetUriPath()
    {
        $request = new lmbHttpRequest('https://test.com/path?foo=1');
        $this->assertEquals('/path', $request->getUri()->getPath());
    }

    function testGet()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('c' => 1), array('d' => 2));
        $this->assertEquals(1, $request->get('c'));
        $this->assertEquals(2, $request->get('d'));
        $this->assertNull($request->get('foo'));
    }

    function testGetGetParam()
    {
        $request = new lmbHttpRequest('https://test.com?get=test', 'GET');
        $this->assertEquals('test', $request->get('get'));
    }

    function testMergePostOverGet()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('a' => 2), array('a' => 3));
        $this->assertEquals(3, $request->get('a'));
    }

    function testGetSafe()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('c' => '<xss>'));
        $this->assertEquals($request->getSafe('c'), htmlspecialchars('<xss>'));
    }

    function testGetRequest()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('c' => 1), array('d' => 2));

        $this->assertEquals(array('c' => 1, 'd' => 2), $request->getRequest());
        $this->assertEquals(1, $request->getRequest('c'));
        $this->assertNull($request->getRequest('b'), 1);

        $this->assertEquals(1, $request->getRequest('b', 1)); // test for default values
        $this->assertEquals(0, $request->getRequest('b', 0));

        $this->assertEquals(array('b' => null, 'c' => 1, 'd' => 2), $request->getRequest(array('b', 'c', 'd')));
    }

    function testGetGet()
    {
        $get = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
        $request = new lmbHttpRequest('https://test.com', 'GET', $get);
        $this->assertEquals($request->getGet(), $get);
        $this->assertEquals(1, $request->getGet('c'));
        $this->assertNull($request->getGet('b'), 1);

        $this->assertEquals('cool', $request->getGet('sambo', 'cool')); // test for default values
        $this->assertEquals(0, $request->getGet('sambo', 0));

        $field_names = array('ju', 'kung', 'sambo');

        $this->assertEquals(array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null), $request->getGet($field_names));
    }

    function testRequestMethod()
    {
        $post = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
        $request = new lmbHttpRequest('https://test.com', 'POST', [], $post);

        $this->assertEquals('POST', $request->getMethod());
        $this->assertNotEquals('GET', $request->getMethod());

        $this->assertTrue($request->isMethod('POST'));
        $this->assertFalse($request->isMethod('OPTION'));
        $this->assertFalse($request->isMethod('get'));

        $this->assertTrue($request->hasPost());

        $request2 = new lmbHttpRequest('https://test.com', 'GET', [], []);

        $this->assertTrue($request2->isMethod('GET'));
        $this->assertFalse($request2->isMethod('option'));
        $this->assertFalse($request2->isMethod('post'));

        $this->assertFalse($request2->hasPost());
    }

    function testGetPost()
    {
        $post = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
        $request = new lmbHttpRequest('https://test.com', 'POST', array(), $post);
        $this->assertEquals($request->getPost(), $post);
        $this->assertEquals(1, $request->getPost('c'));
        $this->assertNull($request->getPost('b'), 1);

        $this->assertEquals('cool', $request->getPost('sambo', 'cool')); // test for default values
        $this->assertEquals(0, $request->getPost('sambo', 0));

        $field_names = array('ju', 'kung', 'sambo');

        $this->assertEquals(array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null), $request->getPost($field_names));
    }

    function testGetFiltered()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('c' => 'c1'));
        $this->assertEquals(1, $request->getFiltered('c', FILTER_SANITIZE_NUMBER_INT));
        $this->assertEquals(1, $request->getFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1));
    }

    function testGetGetFiltered()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('c' => 'c1'));
        $this->assertEquals(1, $request->getGetFiltered('c', FILTER_SANITIZE_NUMBER_INT));
        $this->assertEquals(1, $request->getGetFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1));
    }

    function testGetGetFiltered_Array()
    {
        $request = new lmbHttpRequest('https://test.com', 'GET', array('c' => 'c1', 'ju' => 'jitsu42'));
        $vars = $request->getGetFiltered(
            array('c', 'ju'),
            array('c' => FILTER_SANITIZE_NUMBER_INT, 'ju' => FILTER_SANITIZE_NUMBER_INT)
        );
        $this->assertEquals(1, $vars['c']);
        $this->assertEquals(42, $vars['ju']);
    }

    function testGetPostFiltered()
    {
        $post = array('c' => 'c1');
        $request = new lmbHttpRequest('https://test.com', 'GET', array(), $post);
        $this->assertEquals(1, $request->getPostFiltered('c', FILTER_SANITIZE_NUMBER_INT));
        $this->assertEquals(1, $request->getPostFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1));
    }

    function testGetPostFiltered_Array()
    {
        $post = array('c' => 'c1', 'ju' => 'jitsu42');
        $request = new lmbHttpRequest('https://test.com', 'GET', array(), $post);
        $vars = $request->getPostFiltered(array('c', 'ju'), FILTER_SANITIZE_NUMBER_INT);
        $this->assertEquals(1, $vars['c']);
        $this->assertEquals(42, $vars['ju']);
    }

    function testExtractPortFromHost()
    {
        $old_uri = @$_SERVER['REQUEST_URI'];
        $old_host = @$_SERVER['HTTP_HOST'];

        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['HTTP_HOST'] = 'test.com:8787';
        $_SERVER['HTTPS'] = 'on';

        $request = lmbHttpRequest::createFromGlobals();
        $this->assertEquals('https://test.com:8787/', $request->getUri()->toString());

        $_SERVER['REQUEST_URI'] = $old_uri;
        $_SERVER['HTTP_HOST'] = $old_host;
    }

    function testToString()
    {
        $files = array(
            'file1' => array(
                'name' => 'file',
                'type' => 'file_type',
                'tmp_name' => 'file_tmp_name',
                'size' => 'file_size',
                'error' => 'file_err_code'
            )
        );

        $request = new lmbHttpRequest('https://test.com?z=1',
            'GET',
            array('b' => array('c' => 1)),
            array('d' => 2),
            //only request data(post, get) should be present in result string
            array('cookie' => 2),
            $files);
        $this->assertEquals('https://test.com?b[c]=1&z=1&d=2', $request->toString());
    }

    function testUriQueryOverridesGets()
    {
        $request = new lmbHttpRequest('https://test.com?a=1', 'GET', array('a' => 2), array());

        $this->assertEquals(1, $request->get('a'));
    }

    function testToString_ValidForConstruct_LmbHttpRequest_IfAttributeNoValidStringURL()
    {
        $request = new lmbHttpRequest('https://test.com?z=1', 'GET',
            array('b' => array('c' => '&m=7')),
            array('d' => '?&n=9#top'));
        $request = new lmbHttpRequest($request->toString());

        $this->assertEquals(1, $request->get('z'));
        $this->assertEquals(array('c' => '&m=7'), $request->get('b'));
        $this->assertEquals('?&n=9#top', $request->get('d'));
        $this->assertEquals('', $request->getUri()->getAnchor());

        $this->assertNull($request->get('m'));
        $this->assertNull($request->get('n'));
    }

    function testArrayAccess()
    {
        $request = new lmbHttpRequest('https://test.com/wow?z=123&arr[1]=321');
        $this->assertEquals('321', $request['arr']['1']);
        $this->assertEquals('123', $request['z']);

        $request2 = new lmbHttpRequest('https://test.com/wow?boo[1][prior]=123&arr[1]=321');
        $this->assertEquals('321', $request2->get('arr')['1']);
        $this->assertEquals('123', $request2->get('boo')['1']['prior']);
        $this->assertEquals(['prior' => '123'], $request2->get('boo')['1']);
        $this->assertEquals(['1' => ['prior' => '123']], $request2->get('boo'));
    }

    function testHasTest()
    {
        $request = new lmbHttpRequest('https://test.com/wow?z=1');
        $this->assertTrue($request->has('z'));

        $request2 = new lmbHttpRequest('https://test.com/wow?zar[]=1&zar[]=2');
        $this->assertTrue($request2->has('zar'));
        $this->assertEquals([1, 2], $request2->get('zar'));

        $request3 = new lmbHttpRequest('https://test.com/wow', 'post', [], ['zar' => [1, 3]]);
        $this->assertTrue($request3->has('zar'));
        $this->assertEquals([1, 3], $request3->get('zar'));
    }

    function testGetWithNewUri()
    {
        $request = new lmbHttpRequest('https://test.com/wow?x=2&z=3');
        $uri = new lmbUri('https://test2.com/foo/bar?x=3&z=4');
        $request1 = $request->withUri($uri, true);
        $request2 = $request->withUri($uri);

        $this->assertEquals(3, $request1->get('x'));
        $this->assertEquals('/foo/bar', $request1->getUri()->getPath());

        $this->assertEquals(['test.com'], $request1->getHeader('Host'));
        $this->assertEquals('test.com', $request1->getHeaderLine('Host'));

        $this->assertEquals(['test2.com'], $request2->getHeader('Host'));
        $this->assertEquals('test2.com', $request2->getHeaderLine('Host'));
    }

    function testGetBoolean()
    {
        $request = new lmbHttpRequest('https://test.com/wow?x=2&checkbox=on');
        $request2 = new lmbHttpRequest('https://test.com/wow?x=2&checkbox=1');
        $request3 = new lmbHttpRequest('https://test.com/wow?x=2&checkbox=');

        $this->assertTrue($request->getBoolean('checkbox'));
        $this->assertTrue($request2->getBoolean('checkbox'));
        $this->assertFalse($request3->getBoolean('checkbox'));
    }

    function testGetHeader()
    {
        $_SERVER = [];
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf-8';
        $_SERVER['HTTP_HOST'] = 'test.com';
        $_SERVER['PHP_SELF'] = 'index.php';

        $request = lmbHttpRequest::createFromGlobals();
        $request = $request->withAddedHeader('Accept', ['A', 'B']);

        $this->assertEquals(['text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8', 'A', 'B'], $request->getHeader('Accept'));
        $this->assertEquals(['utf-8'], $request->getHeader('Accept-Charset'));
        $this->assertEquals('utf-8', $request->getHeaderLine('Accept-Charset'));
        $this->assertEquals(['test.com'], $request->getHeader('Host'));
        $this->assertEquals('test.com', $request->getHeaderLine('host'));
    }

    function testStream()
    {
        $request = new lmbHttpRequest('https://test.com/');
        $request = $request->withBody(new lmbHttpStream('string data'));

        $this->assertEquals('str', $request->getBody()->read(3));
        $this->assertEquals('ing data', $request->getBody()->getContents());
        $this->assertTrue($request->getBody()->eof());
        $this->assertEquals(11, $request->getBody()->tell());

        $this->assertEquals('string data', $request->getBody());
    }
}
