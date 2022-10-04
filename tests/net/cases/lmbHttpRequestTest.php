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
    $this->assertEquals($request->getUriPath(), '/path');
  }

  function testGet()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 1), array('d' => 2));
    $this->assertEquals($request->get('c'), 1);
    $this->assertEquals($request->get('d'), 2);
    $this->assertNull($request->get('foo'));
  }

  function testMergePostOverGet()
  {
    $request = new lmbHttpRequest('http://test.com', array('a' => 2), array('a' => 3));
    $this->assertEquals($request->get('a'), 3);
  }

  function testGetSafe()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => '<xss>'));
    $this->assertEquals($request->getSafe('c'), htmlspecialchars('<xss>'));
  }

  function testGetRequest()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 1), array('d' => 2));
    $this->assertEquals($request->getRequest(), array('c' => 1, 'd' => 2));
    $this->assertEquals($request->getRequest('c'), 1);
    $this->assertNull($request->getRequest('b'), 1);

    $this->assertEquals($request->getRequest('b', 1), 1); // test for default values
    $this->assertEquals($request->getRequest('b', 0), 0);

    $this->assertEquals($request->getRequest(array('b', 'c', 'd')), array('b' => null, 'c' => 1, 'd' => 2));
  }

  function testGetGet()
  {
  	$get = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', $get);
    $this->assertEquals($request->getGet(), $get);
    $this->assertEquals($request->getGet('c'), 1);
    $this->assertNull($request->getGet('b'), 1);

    $this->assertEquals($request->getGet('sambo', 'cool'), 'cool'); // test for default values
    $this->assertEquals($request->getGet('sambo', 0), 0);

    $field_names = array('ju', 'kung', 'sambo');

    $this->assertEquals($request->getGet($field_names), array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null));
  }

  function testGetPost()
  {
  	$post = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', array(), $post);
    $this->assertEquals($request->getPost(), $post);
    $this->assertEquals($request->getPost('c'), 1);
    $this->assertNull($request->getPost('b'), 1);

    $this->assertEquals($request->getPost('sambo', 'cool'), 'cool'); // test for default values
    $this->assertEquals($request->getPost('sambo', 0), 0);

    $field_names = array('ju', 'kung', 'sambo');

    $this->assertEquals($request->getPost($field_names), array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null));
  }

  function testGetFiltered()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 'c1'));
    $this->assertEquals($request->getFiltered('c', FILTER_SANITIZE_NUMBER_INT), 1);
    $this->assertEquals($request->getFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1), 1);
  }

  function testGetGetFiltered()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 'c1'));
    $this->assertEquals($request->getGetFiltered('c', FILTER_SANITIZE_NUMBER_INT), 1);
    $this->assertEquals($request->getGetFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1), 1);
  }

  function testGetGetFiltered_Array()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 'c1', 'ju' => 'jitsu42'));
    $vars = $request->getGetFiltered(
        array('c', 'ju'),
        array('c' => FILTER_SANITIZE_NUMBER_INT, 'ju' => FILTER_SANITIZE_NUMBER_INT)
    );
    $this->assertEquals($vars['c'], 1);
    $this->assertEquals($vars['ju'], 42);
  }

  function testGetPostFiltered()
  {
    $post = array('c' => 'c1');
    $request = new lmbHttpRequest('http://test.com', array(), $post);
    $this->assertEquals($request->getPostFiltered('c', FILTER_SANITIZE_NUMBER_INT), 1);
    $this->assertEquals($request->getPostFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1), 1);
  }

  function testGetPostFiltered_Array()
  {
    $post = array('c' => 'c1', 'ju' => 'jitsu42');
    $request = new lmbHttpRequest('http://test.com', array(), $post);
    $vars = $request->getPostFiltered(array('c', 'ju'), FILTER_SANITIZE_NUMBER_INT);
    $this->assertEquals($vars['c'], 1);
    $this->assertEquals($vars['ju'], 42);
  }

  function testGetCookie()
  {
  	$cookie = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', array(), array(), $cookie);
    $this->assertEquals($request->getCookie(), $cookie);
    $this->assertEquals($request->getCookie('c'), 1);
    $this->assertNull($request->getCookie('b'), 1);

    $this->assertEquals($request->getCookie('sambo', 'cool'), 'cool'); // test for default values
    $this->assertEquals($request->getCookie('sambo', 0), 0);

    $field_names = array('ju', 'kung', 'sambo');

    $this->assertEquals($request->getCookie($field_names), array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null));
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

    $request = new lmbHttpRequest('http://test.com', array(), array(), array(), $files);
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

    $request = new lmbHttpRequest('http://test.com', array(), array(), array(), $files);
    $this->assertEquals($request->hasFiles(), true);
    $this->assertEquals($request->hasFiles('form'), true);
    $this->assertEquals($request->hasFiles('not_existed_form'), false);
  }

  function testInitByServerVariables()
  {
    $old_uri = @$_SERVER['REQUEST_URI'];
    $old_host = @$_SERVER['HTTP_HOST'];
    $old_port = @$_SERVER['SERVER_PORT'];

    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = 'test.com';
    $_SERVER['SERVER_PORT'] = '8080';

    $request = new lmbHttpRequest();
    $this->assertEquals($request->getRawUriString(), 'http://test.com:8080/');

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

    $request = new lmbHttpRequest();
    $this->assertEquals($request->getRawUriString(), 'http://test.com:8787/');

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
                                array('b' => array('c' => 1)),
                                array('d' => 2),
                                //only request data(post, get) should be present in result string
                                array('cookie' => 2),
                                $files);
    $this->assertEquals($request->toString(), 'http://test.com?b[c]=1&z=1&d=2');
  }

  function testUriQueryOverridesGets()
  {
    $request = new lmbHttpRequest('http://test.com?a=1', array('a' => 2), array());

    $this->assertEquals($request->get('a'), 1);
  }

  function testToString_ValidForConstruct_LmbHttpRequest_IfAttributeNoValidStringURL()
  {
    $request = new lmbHttpRequest('http://test.com?z=1',
                                array('b' => array('c' => '&m=7')),
                                array('d' => '?&n=9#top'));
    $request = new lmbHttpRequest($request->toString());

    $this->assertEquals($request->get('z'), 1);
    $this->assertEquals($request->get('b'), array('c' => '&m=7'));
    $this->assertEquals($request->get('d'), '?&n=9#top');
    $this->assertEquals($request->getUri()->getAnchor(), '');

    $this->assertNull($request->get('m'));
    $this->assertNull($request->get('n'));
  }

  function testForNotSetReservedParams()
  {
    $request = new lmbHttpRequest('http://test.com?__request=1');
    $this->assertNull($request->get('__request'));
    $this->assertEquals('1', $request->getGet('__request'));
  }

  function testArrayAccess()
  {
    $request = new lmbHttpRequest('http://test.com/wow?z=1');
    $this->assertEquals($request['uri']['path'], '/wow');
    $this->assertEquals($request['get']['z'], '1');
  }
}
