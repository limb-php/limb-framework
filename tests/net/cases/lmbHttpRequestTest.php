<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\net\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbHttpRequest;
use limb\net\src\lmbUri;
use limb\net\src\lmbUploadedFile;

class lmbHttpRequestTest extends TestCase
{
  function testGetUri()
  {
    $request = new lmbHttpRequest('http://test.com');
    $this->assertEquals($request->getUri(), new lmbUri('http://test.com'));
  }

  function testGetUriPath()
  {
    $request = new lmbHttpRequest('http://test.com/path?foo=1');
    $this->assertEquals('/path', $request->getUriPath());
  }

  function testGet()
  {
    $request = new lmbHttpRequest('http://test.com', 'GET', array('c' => 1), array('d' => 2));
    $this->assertEquals(1, $request->get('c'));
    $this->assertEquals(2, $request->get('d'));
    $this->assertNull($request->get('foo'));
  }

  function testMergePostOverGet()
  {
    $request = new lmbHttpRequest('http://test.com', 'GET', array('a' => 2), array('a' => 3));
    $this->assertEquals(3, $request->get('a'));
  }

  function testGetSafe()
  {
    $request = new lmbHttpRequest('http://test.com', 'GET', array('c' => '<xss>'));
    $this->assertEquals($request->getSafe('c'), htmlspecialchars('<xss>'));
  }

  function testGetRequest()
  {
    $request = new lmbHttpRequest('http://test.com', 'GET', array('c' => 1), array('d' => 2));

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
    $request = new lmbHttpRequest('http://test.com', 'GET', $get);
    $this->assertEquals($request->getGet(), $get);
    $this->assertEquals(1, $request->getGet('c'));
    $this->assertNull($request->getGet('b'), 1);

    $this->assertEquals('cool', $request->getGet('sambo', 'cool')); // test for default values
    $this->assertEquals(0, $request->getGet('sambo', 0));

    $field_names = array('ju', 'kung', 'sambo');

    $this->assertEquals(array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null), $request->getGet($field_names));
  }

  function testGetPost()
  {
  	$post = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', 'POST', array(), $post);
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
    $request = new lmbHttpRequest('http://test.com', 'GET', array('c' => 'c1'));
    $this->assertEquals(1, $request->getFiltered('c', FILTER_SANITIZE_NUMBER_INT));
    $this->assertEquals(1, $request->getFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1));
  }

  function testGetGetFiltered()
  {
    $request = new lmbHttpRequest('http://test.com', 'GET', array('c' => 'c1'));
    $this->assertEquals(1, $request->getGetFiltered('c', FILTER_SANITIZE_NUMBER_INT));
    $this->assertEquals(1, $request->getGetFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1));
  }

  function testGetGetFiltered_Array()
  {
    $request = new lmbHttpRequest('http://test.com', 'GET', array('c' => 'c1', 'ju' => 'jitsu42'));
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
    $request = new lmbHttpRequest('http://test.com', 'GET', array(), $post);
    $this->assertEquals(1, $request->getPostFiltered('c', FILTER_SANITIZE_NUMBER_INT));
    $this->assertEquals(1, $request->getPostFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1));
  }

  function testGetPostFiltered_Array()
  {
    $post = array('c' => 'c1', 'ju' => 'jitsu42');
    $request = new lmbHttpRequest('http://test.com', 'GET', array(), $post);
    $vars = $request->getPostFiltered(array('c', 'ju'), FILTER_SANITIZE_NUMBER_INT);
    $this->assertEquals(1, $vars['c']);
    $this->assertEquals(42, $vars['ju']);
  }

  function testGetCookie()
  {
  	$cookie = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', 'GET', array(), array(), $cookie);
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

    $request = new lmbHttpRequest('http://test.com', 'POST', array(), array(), array(), $files);
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

    $request = new lmbHttpRequest('http://test.com', 'POST', array(), array(), array(), $files);
    $this->assertEquals(true, $request->hasFiles());
    $this->assertEquals(true, $request->hasFiles('form'));
    $this->assertEquals(false, $request->hasFiles('not_existed_form'));
  }

    function testHasNoFiles()
    {
        $request = new lmbHttpRequest('http://test.com', 'POST', array(), array(), array(), array());
        $this->assertEquals(false, $request->hasFiles());
    }

  function testInitByServerVariables()
  {
    $old_uri = @$_SERVER['REQUEST_URI'];
    $old_host = @$_SERVER['HTTP_HOST'];
    $old_port = @$_SERVER['SERVER_PORT'];

    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = 'test.com';
    $_SERVER['SERVER_PORT'] = '8080';

    $request = lmbHttpRequest::createFromGlobals();
    $this->assertEquals('http://test.com:8080/', $request->getUri()->toString());

    $_SERVER['REQUEST_URI'] = $old_uri;
    $_SERVER['HTTP_HOST'] = $old_host;
    $_SERVER['SERVER_PORT'] = $old_port;
  }

  function testExtractPortFromHost()
  {
    $old_uri = @$_SERVER['REQUEST_URI'];
    $old_host = @$_SERVER['HTTP_HOST'];

    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = 'test.com:8787';

    $request = lmbHttpRequest::createFromGlobals();
    $this->assertEquals('http://test.com:8787/', $request->getUri()->toString());

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

    $request = new lmbHttpRequest('http://test.com?z=1',
                        'GET',
                                array('b' => array('c' => 1)),
                                array('d' => 2),
                                //only request data(post, get) should be present in result string
                                array('cookie' => 2),
                                $files);
    $this->assertEquals('http://test.com?b[c]=1&z=1&d=2', $request->toString());
  }

  function testUriQueryOverridesGets()
  {
    $request = new lmbHttpRequest('http://test.com?a=1', 'GET', array('a' => 2), array());

    $this->assertEquals(1, $request->get('a'));
  }

  function testToString_ValidForConstruct_LmbHttpRequest_IfAttributeNoValidStringURL()
  {
    $request = new lmbHttpRequest('http://test.com?z=1', 'GET',
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
    $request = new lmbHttpRequest('http://test.com/wow?z=1');
    $this->assertEquals('/wow', $request['uri']->getPath());
    $this->assertEquals('1', $request['get']['z']);
  }

    function testHasTest()
    {
        $request = new lmbHttpRequest('http://test.com/wow?z=1');
        $this->assertTrue($request->has('z'));
    }

    function testAttributes()
    {
        $request = new lmbHttpRequest('http://test.com/wow?z=1');
        $request = $request->withAttribute('attr1', '587');

        $this->assertEquals('587', $request->getAttribute('attr1'));

        $request = $request->withAttribute('attr2', '404');

        $this->assertEquals(['attr1' => '587', 'attr2' => '404'], $request->getAttributes());

        $request = $request->withoutAttribute('attr2');

        $this->assertEquals(['attr1' => '587'], $request->getAttributes());

        $request2 = new lmbHttpRequest('http://test.com/wow?attr=100');
        $request2 = $request2->withAttribute('attr', '200');

        $this->assertEquals(100, $request2->get('attr'));
        $this->assertEquals(200, $request2->getAttribute('attr'));

        $request3 = new lmbHttpRequest('http://test.com/wow3');
        $request3->setAttribute('foo', 'bar');
        $request4 = $request3->withAttribute('foo2', 'bar2');

        $this->assertEquals('bar', $request3->get('foo'));
        $this->assertEquals('bar2', $request4->get('foo2'));
    }

    function testGetWithNewUri()
    {
        $request = new lmbHttpRequest('http://test.com/wow?x=2&z=3');
        $uri = new lmbUri('https://test2.com/foo/bar?x=3&z=4');
        $request1 = $request->withUri($uri, true);
        $request2 = $request->withUri($uri);

        $this->assertEquals(3, $request1->get('x'));
        $this->assertEquals('/foo/bar', $request1->getUriPath());

        $this->assertEquals('test.com', $request1->getHeader('Host'));
        $this->assertEquals('test2.com', $request2->getHeader('Host'));
    }

    function testGetBoolean()
    {
        $request = new lmbHttpRequest('http://test.com/wow?x=2&checkbox=on');
        $request2 = new lmbHttpRequest('http://test.com/wow?x=2&checkbox=1');
        $request3 = new lmbHttpRequest('http://test.com/wow?x=2&checkbox=');

        $this->assertEquals(true, $request->getBoolean('checkbox'));
        $this->assertEquals(true, $request2->getBoolean('checkbox'));
        $this->assertEquals(false, $request3->getBoolean('checkbox'));
    }
}
