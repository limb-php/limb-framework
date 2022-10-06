<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_agent
 */

use limb\web_agent\src\lmbWebServerResponse;
use limb\web_agent\src\lmbWebServerCookieCollection;

/**
 * Web server response
 *
 * @package web_agent
 * @version $Id: lmbWebServerResponseTest.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebServerResponseTest extends TestCase
{

  function testGetResponseParams()
  {
    $cookies = new lmbWebServerCookiesCollection();
    $headers = new lmbWebAgentHeaders(array('Location' => 'http://localhost'));
    $response = new lmbWebServerResponse('content', 200, 'text/html', 'utf-8', $cookies, $headers);

    $this->assertEquals($response->getContent(), 'content');
    $this->assertEquals($response->getStatus(), 200);
    $this->assertEquals($response->getMediaType(), 'text/html');
    $this->assertEquals($response->getCharset(), 'utf-8');
    $this->assertEquals($response->getHeaders()->get('location'), 'http://localhost');
    $this->assertFalse($response->getHeaders()->get('p3p'));
    $this->assertIdentical($response->getCookies(), $cookies);
  }

}
