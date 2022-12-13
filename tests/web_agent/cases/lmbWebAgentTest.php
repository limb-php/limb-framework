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
use limb\web_agent\src\lmbWebAgent;
use limb\web_agent\src\lmbWebServerCookie;
use PHPUnit\Framework\TestCase;
use tests\web_agent\lmbFakeWebAgentRequest;

/**
 * @package web_agent
 * @version $Id: lmbWebAgentTest.php 43 2007-10-05 15:33:11Z
 */
class lmbWebAgentTest extends TestCase {

  function testSavePageCookie()
  {
    $request = new lmbFakeWebAgentRequest();
    $agent = new lmbWebAgent($request);
    $request->response_cookies->add(new lmbWebServerCookie('sid=12345'));

    $agent->doRequest('http://test.ru');
    $agent->doRequest('http://test.ru');
    $this->assertEquals('12345', $request->request_cookies->get('sid'));
    $agent->doRequest('http://test2.ru');
    $this->assertFalse($request->request_cookies->has('sid'));
  }

  function testSaveDomainCookie()
  {
    $request = new lmbFakeWebAgentRequest();
    $agent = new lmbWebAgent($request);
    $request->response_cookies->add(new lmbWebServerCookie('sid=12345; domain=.test.ru'));

    $agent->doRequest('http://test.ru');
    $agent->doRequest('http://test.ru');
    $this->assertEquals('12345', $request->request_cookies->get('sid'));
    $agent->doRequest('http://sub.test.ru');
    $this->assertEquals('12345', $request->request_cookies->get('sid'));
    $agent->doRequest('http://test2.ru');
    $this->assertFalse($request->request_cookies->has('sid'));
  }

  function testSavePathCookie()
  {
    $request = new lmbFakeWebAgentRequest();
    $agent = new lmbWebAgent($request);
    $request->response_cookies->add(new lmbWebServerCookie('sid=12345; path=/test/'));

    $agent->doRequest('http://test.ru');
    $agent->doRequest('http://test.ru/test/index.php');
    $this->assertEquals('12345', $request->request_cookies->get('sid'));
    $agent->doRequest('http://test.ru');
    $this->assertFalse($request->request_cookies->has('sid'));
  }

  function testResponseContent()
  {
    $request = new lmbFakeWebAgentRequest();
    $agent = new lmbWebAgent($request);
    $request->response_content = 'test content';

    $agent->doRequest('http://test.ru');
    $this->assertEquals('test content', $agent->getContent());
  }

  function testSendValues()
  {
    $request = new lmbFakeWebAgentRequest();
    $agent = new lmbWebAgent($request);
    $values = $agent->getValues();
    $values->setName1('value1');
    $values->setName2('value2');
    $http_vals = http_build_query(array('name1' => 'value1', 'name2' => 'value2'));

    $agent->doRequest('http://test.ru');
    $this->assertEquals(15, strpos($request->request_url, $http_vals));
    $agent->doRequest('http://test.ru', 'POST');
    $this->assertEquals($request->request_content, $http_vals);
  }

  function testAcceptCharset()
  {
    $request = new lmbFakeWebAgentRequest();
    $agent = new lmbWebAgent($request);
    $agent->setAcceptCharset('utf-8');

    $agent->doRequest('http://test.ru');
    $this->assertEquals('utf-8', $request->request_accept_charset);
  }

  function testRedirect()
  {
    $request = new lmbFakeWebAgentRequest();
    $agent = new lmbWebAgent($request);
    $agent->getValues()->setTest('1');
    $request->response_headers->set('location', 'http://redirect.ru');

    $agent->doRequest('http://test.ru');
    $this->assertEquals('http://redirect.ru', $request->request_url);
    $this->assertEquals('1', $agent->getValues()->getTest());
  }
}
