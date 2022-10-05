<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\filter_chain\src\lmbFilterChain;
use limb\web_app\src\filter\lmbActionPerformingFilter;
use limb\web_app\src\controller\lmbController;

Mock :: generate('lmbFilterChain', 'MockFilterChain');
Mock :: generate('lmbController', 'MockController');

class lmbActionPerformingFilterTest extends TestCase
{
  var $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testThrowExceptionIfNoDispatchedController()
  {
    $filter = new lmbActionPerformingFilter();

    $fc = new MockFilterChain();
    $fc->expectNever('next');

    try
    {
      $filter->run($fc);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testRunOk()
  {
    $controller = new MockController();
    $controller->expectOnce('performAction');

    $this->toolkit->setDispatchedController($controller);

    $filter = new lmbActionPerformingFilter();

    $fc = new MockFilterChain();
    $fc->expectOnce('next');

    $filter->run($fc);
  }
}

