<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\net\cases;

use limb\net\src\lmbHttpRequest;
use limb\net\src\lmbServerRequest;
use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\net\src\lmbUploadedFile;

class lmbServerRequestTest extends TestCase
{

    function testGetServerParams()
    {
        $serverParams = array('c' => 1, 'd' => 2);

        $request = new lmbServerRequest('GET', 'https://test.com', [], $serverParams);
        $this->assertEquals($serverParams, $request->getServerParams());
        $this->assertNull($request->get('foo'));
    }

    function testInitByServerVariables()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['HTTP_HOST'] = 'test.com';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['HTTPS'] = 'on';

        $uri = 'https://test.com:8080/test_uri/level1';

        $request = new lmbServerRequest('GET', $uri, [], $_SERVER);
        $this->assertEquals($uri, $request->getUri());
        $this->assertEquals('8080', $request->getServerParams()['SERVER_PORT']);
    }

    function testWithCookieParams()
    {
        $cookieParams = array('c' => 1, 'd' => 2);

        $request = new lmbServerRequest('GET', 'https://test.com', []);
        $request = $request->withCookieParams($cookieParams);

        $this->assertEquals($cookieParams, $request->getCookieParams());
    }

    function testGetCookie()
    {
        $cookie = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
        $request = new lmbHttpRequest('https://test.com', 'GET', array(), array(), $cookie);
        $this->assertEquals($request->getCookie(), $cookie);
        $this->assertEquals(1, $request->getCookie('c'));
        $this->assertNull($request->getCookie('b'), 1);

        $this->assertEquals('cool', $request->getCookie('sambo', 'cool')); // test for default values
        $this->assertEquals(0, $request->getCookie('sambo', 0));

        $field_names = array('ju', 'kung', 'sambo');

        $this->assertEquals(array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null), $request->getCookie($field_names));
    }

    function testGetFiles()
    {
        $files = array(
            'form' => array(
                'name' => array(
                    'file1' => 'file',
                    'file2' => 'file',
                ),
                'type' => array(
                    'file1' => 'file_type',
                    'file2' => 'file_type',
                ),
                'tmp_name' => array(
                    'file1' => 'file_tmp_name',
                    'file2' => 'file_tmp_name',
                ),
                'size' => array(
                    'file1' => 'file_size',
                    'file2' => 'file_size',
                ),
                'error' => array(
                    'file1' => 'file_err_code',
                    'file2' => 'file_err_code',
                ),
            ),
        );

        $expected = array(
            'form' => array(
                'file1' => new lmbUploadedFile(array(
                    'name' => 'file',
                    'type' => 'file_type',
                    'tmp_name' => 'file_tmp_name',
                    'size' => 'file_size',
                    'error' => 'file_err_code'
                )),
                'file2' => new lmbUploadedFile(array(
                    'name' => 'file',
                    'type' => 'file_type',
                    'tmp_name' => 'file_tmp_name',
                    'size' => 'file_size',
                    'error' => 'file_err_code'
                )),
            ),
        );

        $request = new lmbHttpRequest('https://test.com', 'POST', array(), array(), array(), $files);
        $this->assertEquals($request->getFiles(), $expected);
        $this->assertEquals($request->getFiles('form'), $expected['form']);

        //files ARE returned with raw get
        $this->assertEquals($request->get('form'), $expected['form']);
    }

    function testHasFiles()
    {
        $files = array(
            'form' => array(
                'name' => array('file1' => 'file'),
                'type' => array('file1' => 'file_type'),
                'tmp_name' => array('file1' => 'file_tmp_name'),
                'size' => array('file1' => 'file_size'),
                'error' => array('file1' => 'file_err_code'),
            ),
        );

        $request = new lmbHttpRequest('https://test.com', 'POST', array(), array(), array(), $files);
        $this->assertTrue($request->hasFiles());
        $this->assertTrue($request->hasFiles('form'));
        $this->assertFalse($request->hasFiles('not_existed_form'));
    }

    function testHasNoFiles()
    {
        $request = new lmbHttpRequest('https://test.com', 'POST', array(), array(), array(), array());
        $this->assertFalse($request->hasFiles());
    }

    function testAttributes()
    {
        $request = new lmbHttpRequest('https://test.com/wow?z=1');
        $request = $request->withAttribute('attr1', '587');

        $this->assertEquals('587', $request->getAttribute('attr1'));

        $request = $request->withAttribute('attr2', '404');

        $this->assertEquals(['attr1' => '587', 'attr2' => '404'], $request->getAttributes());

        $request = $request->withoutAttribute('attr2');

        $this->assertEquals(['attr1' => '587'], $request->getAttributes());

        $request2 = new lmbHttpRequest('https://test.com/wow?attr=100');
        $request2 = $request2->withAttribute('attr', '200');

        $this->assertEquals(100, $request2->get('attr'));
        $this->assertEquals(200, $request2->getAttribute('attr'));

        $request3 = new lmbHttpRequest('https://test.com/wow3');
        $request3 = $request3->withAttribute('foo', 'bar');
        $request4 = $request3->withAttribute('foo2', 'bar2');

        $this->assertEquals('bar', $request3->get('foo'));
        $this->assertEquals('bar2', $request4->get('foo2'));
    }

    function testWithQueryParams()
    {
        $QueryParams = array('c' => '222');

        $request = new lmbServerRequest('GET', 'https://test.com');
        $request = $request->withQueryParams($QueryParams);

        $this->assertEquals($QueryParams, $request->getQueryParams());
        $this->assertEquals($QueryParams['c'], $request->getQueryParams()['c']);
    }


    function testWithParsedBody()
    {
        $post = array('c' => 1, 'd' => 2);

        $request = new lmbServerRequest('POST', 'https://test.com');
        $request = $request->withParsedBody($post);

        $this->assertEquals($post, $request->getParsedBody());
        $this->assertEquals($post['d'], $request->getPost('d'));
    }

}
