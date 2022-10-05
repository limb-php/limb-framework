<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\web_app\src\request\lmbRoutesRequestDispatcher;
use limb\web_app\src\request\lmbRoutes;

class lmbRoutesRequestDispatcherTest extends TestCase
{
  protected $request;
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testDispatch()
  {
    $config_array = array(array('path' => '/:controller/:action',
                                'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->reset('/news');

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEquals($result['controller'], 'news');
    $this->assertEquals($result['action'], 'display');
  }

  function testUseActionFromRequestEvenIfMatchedByRoutes()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->reset('/news/display');
    $this->request->set('action', 'admin_display'); // !!!

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEquals($result['controller'], 'news');
    $this->assertEquals($result['action'], 'admin_display');
  }

  function testUseControllerNameFromRequestEvenIfMatchedByRoutes()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->reset('/news/display');
    $this->request->set('action', 'admin_display'); // !!!
    $this->request->set('controller', 'my_controller'); // !!!

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEquals($result['controller'], 'my_controller');
    $this->assertEquals($result['action'], 'admin_display');
  }

  function testNormalizeUrl()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $dispatcher = new lmbRoutesRequestDispatcher();

    $this->request->getUri()->reset('/news/admin_display');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEquals($result['controller'], 'news');
    $this->assertEquals($result['action'], 'admin_display');

    $this->request->getUri()->reset('/blog////index');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'index');

    $this->request->getUri()->reset('/blog/../bar/index/');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEquals($result['controller'], 'bar');
    $this->assertEquals($result['action'], 'index');
  }

  function testDispatchWithOffset()
  {
    $config_array = array(array('path' => ':controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $dispatcher = new lmbRoutesRequestDispatcher($path_offset = '/www',
                                                 $base_path = 'http://example.com/app/');

    $this->request->getUri()->reset('http://example.com/app/news/admin_display');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEquals($result['controller'], 'news');
    $this->assertEquals($result['action'], 'admin_display');
  }
}


