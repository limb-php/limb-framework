<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\web_app\cases\plain\request;

use PHPUnit\Framework\TestCase;
use limb\web_app\src\request\lmbRoutes;
use limb\net\src\lmbUri;
use limb\toolkit\src\lmbToolkit;

class lmbRoutesDispatchTest extends TestCase
{
  function setUp(): void
  {
    lmbToolkit::save();
  }

  function tearDown(): void
  {
    lmbToolkit::restore();
  }

  function testControllerAndDefaultAction()
  {
    $config = array(array('path' => '/blog',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')),
                    array('path' => '/news',
                          'defaults' => array('controller' => 'Newsline',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], 'display');

    $result = $routes->dispatch('/news');

    $this->assertEquals($result['controller'], 'Newsline');
    $this->assertEquals($result['action'], 'display');

    $this->assertEquals($routes->dispatch('/no_such_url'), array());
  }

  function testAnyController()
  {
    $config = array(array('path' => '/:controller',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'display');

    $result = $routes->dispatch('/news');

    $this->assertEquals($result['controller'], 'news');
    $this->assertEquals($result['action'], 'display');
  }

  function testAnyControllerAndAction()
  {
    $config = array(array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'index');

    $result = $routes->dispatch('/blog');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'display');

    $result = $routes->dispatch('/news/last_news');

    $this->assertEquals($result['controller'], 'news');
    $this->assertEquals($result['action'], 'last_news');
  }

  function testConcreteControllerAndAnyAction()
  {
    $config = array(array('path' => '/blog/:action',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')),
                    array('path' => '/news/:action',
                          'defaults' => array('controller' => 'News',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], 'index');

    $result = $routes->dispatch('/blog');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], 'display');

    $result = $routes->dispatch('/news/last_news');

    $this->assertEquals($result['controller'], 'News');
    $this->assertEquals($result['action'], 'last_news');
  }

  function testUrlToMatchAll()
  {
    $config = array(array('path' => '*',
                          'defaults' => array('controller' => '404',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index');

    $this->assertEquals($result['controller'], '404');
    $this->assertEquals($result['action'], 'display');

    $result = $routes->dispatch('/path/to/heaven');

    $this->assertEquals($result['controller'], '404');
    $this->assertEquals($result['action'], 'display');
  }

  function testExtraParamAfterOthers()
  {
    $config = array(array('path' => '/:controller/:action/*additional',
                          'defaults' => array('controller' => '404',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index/and/many/params');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'index');
    $this->assertEquals($result['additional'], 'and/many/params');
  }

  function testExtraParamDefaultName()
  {
    $config = array(array('path' => '/:controller/:action/*',
                          'defaults' => array('controller' => '404',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index/and/many/params');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'index');
    $this->assertEquals($result['extra'], 'and/many/params');
  }

  function testWithRequirements()
  {
    $config = array(array('path' => 'blog/',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')),
                    array('path' => 'blog/:year/:month/:day',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'archive',
                                              'year' => date('Y'),
                                              'month' => $default_month = date('m'),
                                              'day' => $default_day = date('d')),
                          'requirements' => array('year' => '/(19|20)\d\d/',
                                                  'month' => '/[01]?\d/',
                                                  'day' => '/[0-3]?\d/')),
                    array('path' => 'blog/:action',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/2004/12');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], 'archive');
    $this->assertEquals($result['year'], '2004');
    $this->assertEquals($result['month'], '12');
    $this->assertEquals($result['day'], $default_day);

    $result = $routes->dispatch('/blog/2004');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], 'archive');
    $this->assertEquals($result['year'], '2004');
    $this->assertEquals($result['month'], $default_month);
    $this->assertEquals($result['day'], $default_day);

    $result = $routes->dispatch('/blog/1865');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], '1865');
    $this->assertFalse(isset($result['year']));

    $result = $routes->dispatch('/blog/last_articles');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], 'last_articles');
    $this->assertFalse(isset($result['year']));
  }

  function testUrlWithUrlEncodedParams()
  {
    $config = array(
      array(
        'path' => '/:controller/:action/:email',
        'requirements' => array(
          'email' => '/^[a-z@.-]+$/i'
        )
      )
    );
    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/display/bob-sinclar%40yahoo.com');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'display');
    $this->assertEquals($result['email'], 'bob-sinclar@yahoo.com');
  }

  function testApplyDispatchFilter()
  {
    $config = array(array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display'),
                          'dispatch_filter' => array($this, '_processDispatchResult')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/display');

    $this->assertEquals($result['controller'], 'Blog');
    $this->assertEquals($result['action'], 'display');
  }

  function testExtraDataAfterSecondDelimiter()
  {
     $config = array(array('path' => '/:controller:test/:action',
                           'defaults' => array('action' => 'display')),
                     array('path' => '/test2:controller/:action',
                           'defaults' => array('action' => 'display')),
                     array('path' => '/test3:controller:test5/:action/:id:.htm',
                           'defaults' => array('action' => 'display')),
                     array('path' => '/:controller:test6/:id:.htm',
                           'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blogtest/index');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'index');

    $result = $routes->dispatch('/test2blog');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'display');

    $result = $routes->dispatch('/test3blogtest5/display/test.htm');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'display');
    $this->assertEquals($result['id'], 'test');

  $result = $routes->dispatch('/blogtest6/test.htm');

    $this->assertEquals($result['controller'], 'blog');
    $this->assertEquals($result['action'], 'display');
    $this->assertEquals($result['id'], 'test');
  }

  function _processDispatchResult(&$dispatched)
  {
    if(isset($dispatched['controller']))
      $dispatched['controller'] = lmb_camel_case($dispatched['controller']);
  }
}
