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
use limb\net\src\lmbUri;
use limb\core\src\exception\lmbException;

class lmbUriTest extends TestCase
{
  function testCreate()
  {
    $str = 'http://admin:test@localhost:81/test.php/test?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals('http', $uri->getProtocol());
    $this->assertEquals('localhost', $uri->getHost());
    $this->assertEquals('admin', $uri->getUser());
    $this->assertEquals('test', $uri->getPassword());
    $this->assertEquals('81', $uri->getPort());
    $this->assertEquals('23', $uri->getAnchor());

    $this->assertEquals('bar', $uri->getQueryItem('foo'));
    $this->assertEquals(1, $uri->countQueryItems());

    $this->assertEquals('/test.php/test', $uri->getPath());
    $this->assertEquals(3, $uri->countPath());
    $this->assertEquals(array('', 'test.php', 'test'), $uri->getPathElements());
    $this->assertEquals('', $uri->getPathElement(0));
    $this->assertEquals('test.php', $uri->getPathElement(1));
    $this->assertEquals('test', $uri->getPathElement(2));
  }

  function testCreate_FileProtocolWithoutHost_OnUnix()
  {
    $str = 'file:///dir';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->getProtocol(), 'file');
    $this->assertEquals($uri->getHost(), '');

    $this->assertEquals($uri->getPath(), '/dir');
  }

  function testCreate_FileProtocolWithoutHost_OnWindows()
  {
    $str = 'file://c:\dir\another_dir\\';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->getProtocol(), 'file');
    $this->assertEquals($uri->getHost(), '');

    $this->assertEquals($uri->getPath(), 'c:\dir\another_dir\\');

    $str = 'file://c:/dir/another_dir';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->getProtocol(), 'file');
    $this->assertEquals($uri->getHost(), '');

    $this->assertEquals($uri->getPath(), 'c:/dir/another_dir');

    $str = 'file://c:\dir/another_dir';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->getProtocol(), 'file');
    $this->assertEquals($uri->getHost(), '');

    $this->assertEquals($uri->getPath(), 'c:\dir/another_dir');
  }

  function testCreate_FileProtocolWithHost()
  {
    $str = 'file://user:pass@localhost/dir/file';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->getProtocol(), 'file');
    $this->assertEquals($uri->getUser(), 'user');
    $this->assertEquals($uri->getPassword(), 'pass');
    $this->assertEquals($uri->getHost(), 'localhost');
    $this->assertEquals($uri->getPath(), '/dir/file');

    $str = 'file://user:pass@localhost/c:\dir\file';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->getProtocol(), 'file');
    $this->assertEquals($uri->getUser(), 'user');
    $this->assertEquals($uri->getPassword(), 'pass');
    $this->assertEquals($uri->getHost(), 'localhost');
    // should it be just c:\dir\file ???
    $this->assertEquals($uri->getPath(), '/c:\dir\file');
  }

  function testInvalidUriThrowsException()
  {
    try
    {
      $uri = new lmbUri('http:///');
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testToStringDefault()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->toString(), $str);
  }

  function testToStringNoProtocol()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals(
      $uri->toString(array('user', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'admin:test@localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoUser()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals(
      $uri->toString(array('protocol', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'http://localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoPassword()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals(
      $uri->toString(array('protocol', 'user', 'host', 'port', 'path', 'query', 'anchor')),
      'http://admin@localhost:81/test.php?foo=bar#23'
    );
  }

  function testToStringNoHost()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals(
      $uri->toString(array('protocol', 'user', 'password', 'port', 'path', 'query', 'anchor')),
      '/test.php?foo=bar#23'
    );
  }

  function testToStringNoPath()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals(
      $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'query', 'anchor')),
      'http://admin:test@localhost:81?foo=bar#23'
    );
  }

  function testToStringNoQuery()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals(
      $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'anchor')),
      'http://admin:test@localhost:81/test.php#23'
    );
  }

  function testToStringNoAnchor()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertEquals(
      $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path')),
      'http://admin:test@localhost:81/test.php'
    );
  }

  function testSetQueryString()
  {
    $str = 'http://localhost';

    $uri = new lmbUri($str);

    $uri->setQueryString('foo=bar&bar=foo');

    $this->assertEquals($uri->countQueryItems(), 2);
    $this->assertEquals($uri->getQueryItem('foo'), 'bar');
    $this->assertEquals($uri->getQueryItem('bar'), 'foo');
  }

  function testSetQueryString2()
  {
    $str = 'http://localhost';

    $uri = new lmbUri($str);
    $uri->setQueryString('foo[i1]=1&foo[i2]=2');

    $this->assertEquals($uri->countQueryItems(), 1);
    $this->assertEquals($uri->getQueryItem('foo'), array('i1' => '1', 'i2' => '2'));
  }

  function testNormalizePath()
  {
    $uri = new lmbUri('/foo/bar/../boo.php');
    $uri->normalizePath();
    $this->assertEquals($uri, new lmbUri('/foo/boo.php'));

    $uri->reset('/foo/bar/../../boo.php');
    $uri->normalizePath();
    $this->assertEquals($uri, new lmbUri('/boo.php'));

    $uri->reset('/foo/bar/../boo.php');
    $uri->normalizePath();
    $this->assertEquals($uri, new lmbUri('/foo/boo.php'));

    $uri->reset('/foo//bar//boo.php');
    $uri->normalizePath();
    $this->assertEquals($uri, new lmbUri('/foo/bar/boo.php'));

    $uri->reset('/foo//bar///boo.php');
    $uri->normalizePath();
    $this->assertEquals($uri, $uri = new lmbUri('/foo/bar/boo.php'));
    $this->assertEquals($uri->getPath(), $uri->getPath());
  }

  function testAddQueryItem()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $actual_uri = $uri->withQuery( $uri->getQuery() . '&bar=foo' );

    $this->assertEquals('foo=bar&bar=foo', $actual_uri->getQueryString());
  }

    function testAddQueryItem1()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $actual_uri = $uri->withQueryItem('bar', 'foo');

        $this->assertEquals('foo=bar&bar=foo', $actual_uri->getQueryString());
    }

  function testAddQueryItem2()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $actual_uri = $uri->withQueryItem('foo', 'foo');

    $this->assertEquals('foo=foo', $actual_uri->getQueryString());
  }

  function testAddQueryItem3()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $actual_uri = $uri
        ->withQueryItem('foo', array('i1' => 'bar'))
        ->withQueryItem('bar', 1);

    $this->assertEquals('foo[i1]=bar&bar=1', $actual_uri->getQueryString());
  }

  function testAddQueryItem4()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $actual_uri = $uri
        ->withQueryItem('foo', array('i1' => array('i2' => 'bar')))
        ->withQueryItem('bar', 1);

    $this->assertEquals('foo[i1][i2]=bar&bar=1', $actual_uri->getQueryString());
  }

  function testAddQueryItemUrlencode()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $actual_uri = $uri->withQueryItem('foo', ' foo ');

    $this->assertEquals('foo=+foo+', $actual_uri->getQueryString());
  }

  function testAddQueryItemUrlencode2()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $actual_uri = $uri->withQueryItem('foo', array('i1' => ' bar '));

    $this->assertEquals('foo[i1]=+bar+', $actual_uri->getQueryString());
  }

  function testCompareQueryEqual()
  {
    $str = 'http://admin:test@localhost2:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareQueryNotEqual()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar2#23')
     ));
  }

  function testCompareQueryNotEqual2()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compareQuery(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo#23')
     ));
  }

  function testCompareIdentical()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?foo=bar#23')));
  }

  function testCompareEqual()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?foo=bar&bar=foo#23')));
  }

  function testCompareEqual2()
  {
    $str = 'http://admin:test@localhost:81?';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81')
     ));
  }

  function testCompareNotEqualSchema()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('https://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualUser()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin1:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPassword()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test1@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualHost()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost1:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPort()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost/test.php?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPath()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23')
     ));
  }

  function testCompareNotEqualPath2()
  {
    $str = 'http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertFalse($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php/test1?bar=foo&foo=bar#23')
     ));
  }

  function testCompareAnchorDoesntMatter()
  {
    $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->compare(
      new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#32')
     ));
  }

  function testComparePathEqual()
  {
    $str = 'http://localhost/test.php/test';

    $uri = new lmbUri($str);

    $this->assertEquals(0,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php/test')
      )
    );
  }

  function testComparePathContains()
  {
    $str = 'http://localhost/test.php/test';

    $uri = new lmbUri($str);

    $this->assertEquals(1,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php')
      )
    );
  }

  function testComparePathIsContained()
  {
    $str = 'http://localhost/test.php/test';

    $uri = new lmbUri($str);

    $this->assertEquals(-1,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function testComparePathNotEqual()
  {
    $str = 'http://localhost/test.php/test/test1';

    $uri = new lmbUri($str);

    $this->assertEquals(false,
      $uri->comparePath(
        new lmbUri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function testRemoveQueryItem()
  {
    $str = 'http://localhost/test.php?foo=bar&bar=foo';

    $uri = new lmbUri($str);

    $actual_uri = $uri->withoutQueryItem('bar');

    $this->assertEquals('foo=bar', $actual_uri->getQueryString());
    $this->assertEquals('http://localhost/test.php?foo=bar', $actual_uri->toString());
  }

    function testRemoveQueryItems()
    {
        $str = 'http://localhost/test.php?foo=bar&bar=foo';

        $uri = new lmbUri($str);

        $actual_uri = $uri->withoutQueryItems();

        $this->assertEquals('', $actual_uri->getQueryString());
        $this->assertEquals('http://localhost/test.php', $actual_uri->toString());
    }

  function testRemoveQueryItems2()
  {
    $str = 'http://localhost/test.php?foo=bar&bar=foo';

    $uri = new lmbUri($str);

    $actual_uri = $uri->withQuery('');

    $this->assertEquals('', $actual_uri->getQueryString());
    $this->assertEquals('http://localhost/test.php', $actual_uri->toString());
  }

  function testIsAbsolute()
  {
    $str = '/test.php';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->isAbsolute());
  }

  function testIsAbsoluteNoPath()
  {
    $str = 'http://somedomain.com';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->isAbsolute());
  }

  function testIsRelative()
  {
    $str = '../../test.php';

    $uri = new lmbUri($str);

    $this->assertTrue($uri->isRelative());
  }

  function testSetPath()
  {
    $uri1 = new lmbUri('/index.html');
    $uri2 = new lmbUri('http://dot.com/index.html');

    $this->assertEquals($uri1->getPathElements(), $uri2->getPathElements());
  }

  function testGetPathToLevel()
  {
    $uri = new lmbUri('/path/to/level');

    $this->assertEquals($uri->getPathToLevel(1), '/path');
    $this->assertEquals($uri->getPathToLevel(2), '/path/to');
    $this->assertEquals($uri->getPathToLevel(3), '/path/to/level');
    $this->assertEquals($uri->getPathToLevel(4), '');
  }

  function testGetPathFromLevel()
  {
    $uri = new lmbUri('/path/to/level');

    $this->assertEquals($uri->getPathFromLevel(0), '/path/to/level');
    $this->assertEquals($uri->getPathFromLevel(1), '/path/to/level');
    $this->assertEquals($uri->getPathFromLevel(2), '/to/level');
    $this->assertEquals($uri->getPathFromLevel(3), '/level');
    $this->assertEquals($uri->getPathFromLevel(4), '/');
  }

  function testUrlencodedPartsOfQueryAreDecoded()
  {
    $uri = new lmbUri('index.html?wow=' . urlencode('what a nice weather'));
    $this->assertEquals($uri->getQueryItem('wow'), 'what a nice weather');
  }

  function testToString_IfAttributeNoValidStringURL()
  {
    $str = 'http://admin:test@localhost:81/test.php?foo=' . urlencode('10&b=11') . '#23';

    $uri = new lmbUri($str);

    $this->assertEquals($uri->toString(), $str);
  }

  function testUrlDecode()
  {
    $test_value = '+text';
    $uri = new lmbUri('/index.html?var=' . urlencode($test_value));

    $q_items = $uri->getQueryItems();
    $this->assertEquals($q_items['var'], $test_value);
  }
}
