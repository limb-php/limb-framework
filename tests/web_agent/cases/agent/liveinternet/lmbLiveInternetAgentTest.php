<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_agent\cases\agent\liveinternet;

/**
 * @package web_agent
 */

use limb\web_agent\src\lmbWebServerCookie;
use PHPUnit\Framework\TestCase;
use limb\web_agent\src\agent\liveinternet\lmbLiveInternetAgent;
use tests\web_agent\lmbFakeWebAgentRequest;

/**
 * @package web_agent
 * @version $Id: lmbLiveInternetAgentTest.php 89 2007-10-12 15:28:50Z
 */
class lmbLiveInternetAgentTest extends TestCase {
  protected $agent;
  protected $request;

  function setUp(): void
  {
    $this->request = new lmbFakeWebAgentRequest();
  	$this->agent = new lmbLiveInternetAgent('test.ru', $this->request);
  }

  function testGetProject()
  {
    $this->assertEquals($this->agent->getProject(), 'test.ru');
  }

  function testGetValues()
  {
    $arr = array(
      'test' => 'val',
      'test1' => 'val1',
      'id' => array(9,7,5,0));
    $vals = $this->agent->getValues();
    $vals->import($arr);

    $this->assertEquals($vals->buildQuery(), 'test=val;test1=val1;id=9;id=7;id=5;id=0');
  }

  function testRequestStatPage()
  {
  	$this->agent->requestStatPage('visitors.html');

    $this->assertEquals($this->request->request_url, 'http://www.liveinternet.ru/stat/test.ru/visitors.html');
  }

  function testAuth()
  {
    $this->request->response_cookies->add(new lmbWebServerCookie('sid=zxc'));
  	$this->agent->auth('***');

    $this->assertEquals($this->request->request_url, 'http://www.liveinternet.ru/stat/test.ru/');
    $this->assertEquals($this->request->request_content,
      http_build_query(array('url' => 'http://test.ru', 'password' => '***', 'ok' => ' ok ')));
    $this->assertEquals($this->agent->getCookies()->get(0)->value, 'zxc');
  }
}
