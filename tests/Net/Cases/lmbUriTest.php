<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\Net\Cases;

use PHPUnit\Framework\TestCase;
use limb\net\lmbUriHelper;
use limb\net\lmbUri;
use limb\core\exception\lmbException;

class lmbUriTest extends TestCase
{
    function testCreate()
    {
        $str = 'http://admin:test@localhost:81/test.php/test?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('localhost', $uri->getHost());
        $this->assertEquals('admin', $uri->getUser());
        $this->assertEquals('test', $uri->getPassword());
        $this->assertEquals('81', $uri->getPort());
        $this->assertEquals('23', $uri->getAnchor());

        $this->assertEquals('bar', $uri->getQueryItem('foo'));
        $this->assertEquals(1, $uri->countQueryItems());

        $this->assertEquals('/test.php/test', $uri->getPath());
        $this->assertEquals(3, $uri->countPath());
        $this->assertEquals(array('', 'test.php', 'test'), lmbUriHelper::getPathElements($uri));
        $this->assertEquals('', lmbUriHelper::getPathElement($uri, 0));
        $this->assertEquals('test.php', lmbUriHelper::getPathElement($uri, 1));
        $this->assertEquals('test', lmbUriHelper::getPathElement($uri, 2));
    }

    function testCreate_FileProtocolWithoutHost_OnUnix()
    {
        $str = 'file:///dir';

        $uri = new lmbUri($str);

        $this->assertEquals('file', $uri->getScheme());
        $this->assertEquals('', $uri->getHost());

        $this->assertEquals('/dir', $uri->getPath());
    }

    function testCreate_FileProtocolWithoutHost_OnWindows()
    {
        $str = 'file://c:\dir\another_dir\\';

        $uri = new lmbUri($str);

        $this->assertEquals('file', $uri->getScheme());
        $this->assertEquals('', $uri->getHost());

        $this->assertEquals('c:\dir\another_dir\\', $uri->getPath());

        $str = 'file://c:/dir/another_dir';

        $uri = new lmbUri($str);

        $this->assertEquals('file', $uri->getScheme());
        $this->assertEquals('', $uri->getHost());

        $this->assertEquals('c:/dir/another_dir', $uri->getPath());

        $str = 'file://c:\dir/another_dir';

        $uri = new lmbUri($str);

        $this->assertEquals('file', $uri->getScheme());
        $this->assertEquals('', $uri->getHost());

        $this->assertEquals('c:\dir/another_dir', $uri->getPath());
    }

    function testCreate_FileProtocolWithHost()
    {
        $str = 'file://user:pass@localhost/dir/file';

        $uri = new lmbUri($str);

        $this->assertEquals('file', $uri->getScheme());
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('pass', $uri->getPassword());
        $this->assertEquals('localhost', $uri->getHost());
        $this->assertEquals('/dir/file', $uri->getPath());

        $str = 'file://user:pass@localhost/c:\dir\file';

        $uri = new lmbUri($str);

        $this->assertEquals('file', $uri->getScheme());
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('pass', $uri->getPassword());
        $this->assertEquals('localhost', $uri->getHost());
        // should it be just c:\dir\file ???
        $this->assertEquals('/c:\dir\file', $uri->getPath());
    }

    function testInvalidUriThrowsException()
    {
        try {
            $uri = new lmbUri('http:///');
            $this->fail();
        } catch (lmbException $e) {
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
            'admin:test@localhost:81/test.php?foo=bar#23',
            $uri->toString(array('user', 'password', 'host', 'port', 'path', 'query', 'anchor'))
        );
    }

    function testToStringNoUser()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertEquals(
            'http://localhost:81/test.php?foo=bar#23',
            $uri->toString(array('protocol', 'password', 'host', 'port', 'path', 'query', 'anchor'))
        );
    }

    function testToStringNoPassword()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertEquals(
            'http://admin@localhost:81/test.php?foo=bar#23',
            $uri->toString(array('protocol', 'user', 'host', 'port', 'path', 'query', 'anchor'))
        );
    }

    function testToStringNoHost()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertEquals(
            '/test.php?foo=bar#23',
            $uri->toString(array('protocol', 'user', 'password', 'port', 'path', 'query', 'anchor'))
        );
    }

    function testToStringNoPath()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertEquals(
            'http://admin:test@localhost:81?foo=bar#23',
            $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'query', 'anchor'))
        );
    }

    function testToStringNoQuery()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertEquals(
            'http://admin:test@localhost:81/test.php#23',
            $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path', 'anchor'))
        );
    }

    function testToStringNoAnchor()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertEquals(
            'http://admin:test@localhost:81/test.php',
            $uri->toString(array('protocol', 'user', 'password', 'host', 'port', 'path'))
        );
    }

    function testWithQuery()
    {
        $str = 'http://localhost';

        $uri = new lmbUri($str);

        $actual_uri = $uri->withQuery('foo=bar&bar=foo');

        $this->assertEquals(2, $actual_uri->countQueryItems());
        $this->assertEquals('bar', $actual_uri->getQueryItem('foo'));
        $this->assertEquals('foo', $actual_uri->getQueryItem('bar'));
    }

    function testWithQuery2()
    {
        $str = 'http://localhost';

        $uri = new lmbUri($str);
        $actual_uri = $uri->withQuery('foo[i1]=1&foo[i2]=2');

        $this->assertEquals(1, $actual_uri->countQueryItems());
        $this->assertEquals(array('i1' => '1', 'i2' => '2'), $actual_uri->getQueryItem('foo'));
    }

    function testNormalizePath()
    {
        $uri = new lmbUri('/foo/bar/../boo.php');
        $uri = $uri->withPath( lmbUriHelper::normalizePath($uri) );

        $this->assertEquals($uri, new lmbUri('/foo/boo.php'));

        $uri = new lmbUri('/foo/bar/../../boo.php');
        $uri = $uri->withPath( lmbUriHelper::normalizePath($uri) );

        $this->assertEquals($uri, new lmbUri('/boo.php'));

        $uri = new lmbUri('/foo/bar/../boo.php');
        $uri = $uri->withPath( lmbUriHelper::normalizePath($uri) );

        $this->assertEquals($uri, new lmbUri('/foo/boo.php'));

        $uri = new lmbUri('/foo//bar//boo.php');
        $uri = $uri->withPath( lmbUriHelper::normalizePath($uri) );

        $this->assertEquals($uri, new lmbUri('/foo/bar/boo.php'));

        $uri = new lmbUri('/foo//bar///boo.php');
        $uri = $uri->withPath( lmbUriHelper::normalizePath($uri) );

        $this->assertEquals($uri, $uri = new lmbUri('/foo/bar/boo.php'));
        $this->assertEquals($uri->getPath(), $uri->getPath());
    }

    function testAddQueryItem()
    {
        $str = 'http://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $actual_uri = $uri->withQuery($uri->getQuery() . '&bar=foo');

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

        $this->assertTrue(lmbUriHelper::compareQuery(
            $uri,
            new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
        ));
    }

    function testCompareQueryNotEqual()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compareQuery(
            $uri,
            new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar2#23')
        ));
    }

    function testCompareQueryNotEqual2()
    {
        $str = 'https://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compareQuery(
            $uri,
            new lmbUri('https://admin:test@localhost:81/test.php?bar=foo#23')
        ));
    }

    function testCompareIdentical()
    {
        $str = 'https://admin:test@localhost:81/test.php?foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertTrue(lmbUriHelper::compare(
            $uri,
            new lmbUri('https://admin:test@localhost:81/test.php?foo=bar#23')
        ));
    }

    function testCompareEqual()
    {
        $str = 'https://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertTrue(lmbUriHelper::compare(
            $uri,
            new lmbUri('https://admin:test@localhost:81/test.php?foo=bar&bar=foo#23')
        ));
    }

    function testCompareEqual2()
    {
        $str = 'http://admin:test@localhost:81?';

        $uri = new lmbUri($str);

        $this->assertTrue(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin:test@localhost:81')
        ));
    }

    function testCompareNotEqualSchema()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compare(
            $uri,
            new lmbUri('https://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
        ));
    }

    function testCompareNotEqualUser()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin1:test@localhost:81/test.php?bar=foo&foo=bar#23')
        ));
    }

    function testCompareNotEqualPassword()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin:test1@localhost:81/test.php?bar=foo&foo=bar#23')
        ));
    }

    function testCompareNotEqualHost()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin:test@localhost1:81/test.php?bar=foo&foo=bar#23')
        ));
    }

    function testCompareNotEqualPort()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin:test@localhost/test.php?bar=foo&foo=bar#23')
        ));
    }

    function testCompareNotEqualPath()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23')
        ));
    }

    function testCompareNotEqualPath2()
    {
        $str = 'http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertFalse(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin:test@localhost:81/test.php/test1?bar=foo&foo=bar#23')
        ));
    }

    function testCompareAnchorDoesntMatter()
    {
        $str = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

        $uri = new lmbUri($str);

        $this->assertTrue(lmbUriHelper::compare(
            $uri,
            new lmbUri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#32')
        ));
    }

    function testComparePathEqual()
    {
        $str = 'http://localhost/test.php/test';

        $uri = new lmbUri($str);

        $this->assertEquals(0,
            lmbUriHelper::comparePath(
                $uri,
                new lmbUri('http://localhost2/test.php/test')
            )
        );
    }

    function testComparePathContains()
    {
        $str = 'http://localhost/test.php/test';

        $uri = new lmbUri($str);

        $this->assertEquals(1,
            lmbUriHelper::comparePath(
                $uri,
                new lmbUri('http://localhost2/test.php')
            )
        );
    }

    function testComparePathIsContained()
    {
        $str = 'http://localhost/test.php/test';

        $uri = new lmbUri($str);

        $this->assertEquals(-1,
            lmbUriHelper::comparePath(
                $uri,
                new lmbUri('http://localhost2/test.php/test/test2')
            )
        );
    }

    function testComparePathNotEqual()
    {
        $str = 'http://localhost/test.php/test/test1';

        $uri = new lmbUri($str);

        $this->assertEquals(false,
            lmbUriHelper::comparePath(
                $uri,
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

        $this->assertEquals(lmbUriHelper::getPathElements($uri1), lmbUriHelper::getPathElements($uri2));
    }

    function testGetPathToLevel()
    {
        $uri = new lmbUri('/path/to/level');

        $this->assertEquals('/path', lmbUriHelper::getPathToLevel($uri, 1));
        $this->assertEquals('/path/to', lmbUriHelper::getPathToLevel($uri, 2));
        $this->assertEquals('/path/to/level', lmbUriHelper::getPathToLevel($uri, 3));
        $this->assertEquals('', lmbUriHelper::getPathToLevel($uri, 4));
    }

    function testGetPathFromLevel()
    {
        $uri = new lmbUri('/path/to/level/level2');

        $this->assertEquals('/path/to/level/level2', lmbUriHelper::getPathFromLevel($uri, 0));
        $this->assertEquals('/path/to/level/level2', lmbUriHelper::getPathFromLevel($uri, 1));
        $this->assertEquals('/to/level/level2', lmbUriHelper::getPathFromLevel($uri, 2));
        $this->assertEquals('/level/level2', lmbUriHelper::getPathFromLevel($uri, 3));
        $this->assertEquals('/level2', lmbUriHelper::getPathFromLevel($uri, 4));
        $this->assertEquals('/', lmbUriHelper::getPathFromLevel($uri, 5));
    }

    function testUrlencodedPartsOfQueryAreDecoded()
    {
        $uri = new lmbUri('index.html?wow=' . urlencode('what a nice weather'));
        $this->assertEquals('what a nice weather', $uri->getQueryItem('wow'));
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

    function testSpecificProtocolName()
    {
        $uri = new lmbUri('svn+ssh://ms.org');

        $this->assertEquals('svn+ssh://ms.org', $uri->__toString());
        $this->assertEquals('svn+ssh', $uri->getScheme());
        $this->assertEquals('ms.org', $uri->getHost());
    }

    function testWithFragment()
    {
        $str = 'https://localhost/query';
        $uri = new lmbUri($str);
        $actual_uri = $uri->withFragment('fragment1');

        $this->assertEquals($str . '#fragment1', $actual_uri->__toString());
        $this->assertEquals(new lmbUri($str . '#fragment1'), $actual_uri);
    }

    function testWithFragmentPath()
    {
        $str = 'https://localhost/query';
        $uri = new lmbUri($str);
        $actual_uri = $uri
            ->withFragment('fragment1')
            ->withPath('/new_query/test');

        $this->assertEquals('https://localhost/new_query/test#fragment1', $actual_uri->__toString());
    }
}
