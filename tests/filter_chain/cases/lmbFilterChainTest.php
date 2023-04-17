<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\filter_chain\tests\cases;

require_once ('.setup.php');

use limb\net\src\lmbHttpRequest;
use limb\net\src\lmbHttpResponse;
use PHPUnit\Framework\TestCase;
use limb\filter_chain\src\lmbFilterChain;

class InterceptingFilterStub
{
  var $captured = array();
  var $run = false;

  function run($fc, $request, $response): lmbHttpResponse
  {
    $this->run = true;
    $this->captured['filter_chain'] = $fc;

    return $fc->next($request, $response);
  }
}

class OutputFilter1
{
  function run($fc, $request, $response): lmbHttpResponse
  {
    echo '<filter1>';
    $response = $fc->next($request, $response);
    echo '</filter1>';

    return $response;
  }
}

class OutputFilter2
{
  function run($fc, $request, $response): lmbHttpResponse
  {
    echo '<filter2>';
    $response = $fc->next($request, $response);
    echo '</filter2>';

    return $response;
  }
}

class OutputFilter3
{
  function run($fc, $request, $response): lmbHttpResponse
  {
    echo '<filter3>';
    $response = $fc->next($request, $response);
    echo '</filter3>';

    return $response;
  }
}

class lmbFilterChainTest extends TestCase
{
  var $fc;

  function setUp(): void
  {
      $this->request = new lmbHttpRequest();
      $this->response = new lmbHttpResponse();

      $this->fc = new lmbFilterChain();
  }

  function testProcess()
  {
    $mock_filter = new InterceptingFilterStub();

    $this->fc->registerFilter($mock_filter);

    $this->assertFalse($mock_filter->run);

    $this->fc->process($this->request, $this->response);

    $this->assertTrue($mock_filter->run);

    $this->assertInstanceOf(lmbFilterChain::class, $mock_filter->captured['filter_chain']);
  }

  function testProcessProperNesting()
  {
    $f1 = new OutputFilter1();
    $f2 = new OutputFilter2();

    $this->fc->registerFilter($f1);
    $this->fc->registerFilter($f2);

    ob_start();

    $this->fc->process($this->request, $this->response);

    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEquals('<filter1><filter2></filter2></filter1>', $str);
  }

  function testFilterChainAsAFilter()
  {
    $f1 = new OutputFilter1();
    $f2 = new OutputFilter2();

    $fc = new lmbFilterChain();

    $fc1 = new lmbFilterChain();
    $fc1->registerFilter($f1);

    $fc2 = new lmbFilterChain();
    $fc2->registerFilter($f2);

    $fc->registerFilter($fc1);
    $fc->registerFilter($fc2);

    ob_start();

    $fc->process(
        request(),
        response()
    );

    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEquals('<filter1></filter1><filter2></filter2>', $str);
  }
}
