<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\web_app\cases\plain\request;

use PHPUnit\Framework\TestCase;
use limb\web_app\src\request\lmbCompositeRequestDispatcher;
use limb\web_app\src\request\lmbRequestDispatcherInterface;
use limb\net\src\lmbHttpRequest;

require_once dirname(__FILE__) . '/../../.setup.php';

class lmbCompositeRequestDispatcherTest extends TestCase
{
  protected $request;

  function setUp(): void
  {
    $this->request = new lmbHttpRequest();
  }

  protected function _setUpMocks($dispatcher, $result1, $result2)
  {
    $mock_dispatcher1 = $this->createMock(lmbRequestDispatcherInterface::class);
    $mock_dispatcher2 = $this->createMock(lmbRequestDispatcherInterface::class);

    $dispatcher->addDispatcher($mock_dispatcher1);
    $dispatcher->addDispatcher($mock_dispatcher2);

    $this->_setUpMockDispatcher($mock_dispatcher1, $result1);
    $this->_setUpMockDispatcher($mock_dispatcher2, $result2);
  }

  protected function _setUpMockDispatcher($mock_dispatcher, $result)
  {
    if($result !== null)
    {
      $mock_dispatcher
          ->expects($this->once())
          ->method('dispatch')
          ->with($this->request)
          ->willReturn($result);
    }
    else {
        $mock_dispatcher->expects($this->never())->method('dispatch');
    }
  }

  function testDispatchOkByFirstDispatcher()
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    $this->_setUpMocks($dispatcher, $result = array('controller' => 'whatever'), null);
    $this->assertEquals($dispatcher->dispatch($this->request), $result);
  }

  function testDispatchOkBySecondDispatcherSinceFirstReturnNoController()
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    $this->_setUpMocks($dispatcher,
                       array('any_param' => 'whatever'),
                       $result = array('controller' => 'whatever'));
    $this->assertEquals($dispatcher->dispatch($this->request), $result);
  }

  function testReturnEmptyArraySinceAllDispatchersCantDispatchController()
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    $this->_setUpMocks($dispatcher,
                       array('any_param1' => 'whatever'),
                       array('any_param1' => 'anything'));
    $this->assertEquals(array(), $dispatcher->dispatch($this->request));
  }
}
