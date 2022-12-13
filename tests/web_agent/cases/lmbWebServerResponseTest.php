<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_agent\cases;

/**
 * @package web_agent
 */

use limb\web_agent\src\lmbWebAgentHeaders;
use limb\web_agent\src\lmbWebServerCookiesCollection;
use limb\web_agent\src\lmbWebServerResponse;
use PHPUnit\Framework\TestCase;

/**
 * Web server response
 *
 * @package web_agent
 * @version $Id: lmbWebServerResponseTest.php 40 2007-10-04 15:52:39Z
 */
class lmbWebServerResponseTest extends TestCase
{

  function testGetResponseParams()
  {
    $cookies = new lmbWebServerCookiesCollection();
    $headers = new lmbWebAgentHeaders(array('Location' => 'http://localhost'));
    $response = new lmbWebServerResponse('content', 200, 'text/html', 'utf-8', $cookies, $headers);

    $this->assertEquals('content', $response->getContent());
    $this->assertEquals(200, $response->getStatus());
    $this->assertEquals('text/html', $response->getMediaType());
    $this->assertEquals('utf-8', $response->getCharset());
    $this->assertEquals('http://localhost', $response->getHeaders()->get('location'));
    $this->assertNull($response->getHeaders()->get('p3p'));
    $this->assertEquals($response->getCookies(), $cookies);
  }
}
