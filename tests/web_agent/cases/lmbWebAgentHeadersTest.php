<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_agent\cases;

use limb\web_agent\src\lmbWebAgentHeaders;
use PHPUnit\Framework\TestCase;

/**
 * @package web_agent
 * @version $Id: lmbWebAgentHeadersTest.php 40 2007-10-04 15:52:39Z
 */
class lmbWebAgentHeadersTest extends TestCase {

  protected function _getHeaders(): lmbWebAgentHeaders
  {
    return new lmbWebAgentHeaders(array('GET / HTTP/1.1' => null, 'Host' => 'google.com'));
  }

  function testGetSet()
  {
    $headers = $this->_getHeaders();

    $this->assertEquals('google.com', $headers->get('host'));

    $headers->set('User-Agent', 'TestAgent');
    $this->assertEquals('TestAgent', $headers->get('user-agent'));
  }

  function testSetRaw()
  {
    $headers = $this->_getHeaders();

    $headers->setRaw('Content-MD5', 'zxc');
    $this->assertEquals('zxc', $headers->get('Content-MD5'));
  }

  function testGetFirst()
  {
    $headers = $this->_getHeaders();

    $this->assertEquals('GET / HTTP/1.1', $headers->getFirst());
  }

  function testHasHeader()
  {
    $headers = $this->_getHeaders();

    $this->assertTrue($headers->has('host'));
    $this->assertFalse($headers->has('user-agent'));
  }

  function testClean()
  {
    $headers = $this->_getHeaders();

    $this->assertTrue($headers->has('host'));
    $headers->clean();
    $this->assertFalse($headers->has('host'));
  }

  function testCountHeaders()
  {
    $headers = $this->_getHeaders();

    $this->assertEquals(1, $headers->countHeaders('host'));
    $this->assertEquals(0, $headers->countHeaders('user-agent'));

    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');
    $this->assertEquals(2, $headers->countHeaders('set-cookie'));
  }

  function testGetByNum()
  {
    $headers = $this->_getHeaders();

    $this->assertEquals('test.ru', $headers->get('host', 0));
    $this->assertNull($headers->get('host', 1));

    $this->assertNull($headers->get('user-agent', 0));

    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');
    $this->assertEquals('sid=1', $headers->get('set-cookie', 0));
    $this->assertEquals('sid2=2', $headers->get('set-cookie', 1));
    $this->assertNull($headers->get('set-cookie', 3));
  }

  function testParse()
  {
  	$headers = new lmbWebAgentHeaders();

    $this->assertTrue($headers->parse("GET / HTTP/1.1\r\n"));
    $this->assertTrue($headers->parse('Host: google.com'));
    $this->assertTrue($headers->parse('Set-Cookie: sid=1'));
    $this->assertTrue($headers->parse('Set-Cookie: sid2=2'));
    $this->assertFalse($headers->parse("\r\n"));

    $this->assertEquals('GET / HTTP/1.1', $headers->getFirst());
    $this->assertEquals('google.com', $headers->get('host'));
    $this->assertEquals('sid=1', $headers->get('set-cookie', 0));
    $this->assertEquals('sid2=2', $headers->get('set-cookie', 1));
  }

  function testExportHeader()
  {
    $headers = $this->_getHeaders();

    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');
    $this->assertEquals('Host: google.com', $headers->exportHeader('host'));
    $this->assertEquals('Set-Cookie: sid=1', $headers->exportHeader('set-cookie', 0));
    $this->assertEquals('Set-Cookie: sid2=2', $headers->exportHeader('set-cookie', 1));
    $this->assertFalse($headers->exportHeader('set-cookie', 3));
  }

  function testExportHeaders()
  {
    $headers = $this->_getHeaders();
    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');

    $str = $headers->exportHeaders();
    $this->assertEquals("GET / HTTP/1.1\r\nHost: google.com\r\nSet-Cookie: sid=1\r\nSet-Cookie: sid2=2\r\n", $str);
  }

  function testRemove()
  {
    $headers = $this->_getHeaders();
    $headers->set('User-Agent', 'TestAgent');

    $this->assertTrue($headers->has('host'));
    $headers->remove('host');
    $this->assertFalse($headers->has('host'));
    $this->assertEquals('TestAgent', $headers->get('user-agent'));
  }

  function testCopyTo()
  {
    $headers = $this->_getHeaders();
    $headers->set('Set-Cookie', 'sid=1');
    $headers->set('Set-Cookie', 'sid2=2');

    $headers_dest = new lmbWebAgentHeaders();
    $headers->copyTo($headers_dest);
    $str = $headers_dest->exportHeaders();
    $this->assertEquals("GET / HTTP/1.1\r\nHost: google.com\r\nSet-Cookie: sid=1\r\nSet-Cookie: sid2=2\r\n", $str);
  }

}
