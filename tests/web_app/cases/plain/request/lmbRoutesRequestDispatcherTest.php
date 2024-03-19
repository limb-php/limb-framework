<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\request;

require_once dirname(__FILE__) . '/../../init.inc.php';

use limb\net\src\lmbUri;
use PHPUnit\Framework\TestCase;
use limb\web_app\src\request\lmbRoutesRequestDispatcher;
use limb\web_app\src\request\lmbRoutes;
use limb\toolkit\src\lmbToolkit;

class lmbRoutesRequestDispatcherTest extends TestCase
{
    protected $request;
    protected $toolkit;

    function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
        $this->request = $this->toolkit->getRequest();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testDispatch()
    {
        $config_array = array(
            array(
                'path' => '/:controller/:action',
                'defaults' => array('action' => 'display')
            )
        );
        $routes = new lmbRoutes($config_array);
        $this->toolkit->setRoutes($routes);

        $uri = new lmbUri('/news');
        $this->request = $this->request->withUri($uri);

        $dispatcher = new lmbRoutesRequestDispatcher();
        $result = $dispatcher->dispatch($this->request);

        $this->assertEquals('news', $result['controller']);
        $this->assertEquals('display', $result['action']);
    }

    function testUseActionFromRequestEvenIfMatchedByRoutes()
    {
        $config_array = array(
            array('path' => '/:controller/:action')
        );
        $routes = new lmbRoutes($config_array);
        $this->toolkit->setRoutes($routes);

        $uri = new lmbUri('/news/display');
        $uri = $uri->withQueryItem('action', 'admin_display');
        $this->request = $this->request->withUri($uri);

        $dispatcher = new lmbRoutesRequestDispatcher();
        $result = $dispatcher->dispatch($this->request);

        $this->assertEquals('news', $result['controller']);
        $this->assertEquals('admin_display', $result['action']);
    }

    function testUseControllerNameFromRequestEvenIfMatchedByRoutes()
    {
        $config_array = array(
            array('path' => '/:controller/:action')
        );
        $routes = new lmbRoutes($config_array);
        $this->toolkit->setRoutes($routes);

        $this->request->getUri()->withPath('/news/display');
        $this->request->set('action', 'admin_display'); // !!!
        $this->request->set('controller', 'my_controller'); // !!!

        $dispatcher = new lmbRoutesRequestDispatcher();
        $result = $dispatcher->dispatch($this->request);

        $this->assertEquals('my_controller', $result['controller']);
        $this->assertEquals('admin_display', $result['action']);
    }

    function testNormalizeUrl()
    {
        $config_array = array(
            array('path' => '/:controller/:action')
        );
        $routes = new lmbRoutes($config_array);
        $this->toolkit->setRoutes($routes);

        $dispatcher = new lmbRoutesRequestDispatcher();

        $uri = new lmbUri('/news/admin_display');
        $this->request = $this->request->withUri($uri);
        $result = $dispatcher->dispatch($this->request);

        $this->assertEquals('news', $result['controller']);
        $this->assertEquals('admin_display', $result['action']);

        $uri = new lmbUri('/blog////index');
        $this->request = $this->request->withUri($uri);
        $result = $dispatcher->dispatch($this->request);

        $this->assertEquals('blog', $result['controller']);
        $this->assertEquals('index', $result['action']);

        $uri = new lmbUri('/blog/../bar/index/');
        $this->request = $this->request->withUri($uri);
        $result = $dispatcher->dispatch($this->request);

        $this->assertEquals('bar', $result['controller']);
        $this->assertEquals('index', $result['action']);
    }

    function testDispatchWithOffset()
    {
        $config_array = array(
            array('path' => ':controller/:action')
        );
        $routes = new lmbRoutes($config_array);
        $this->toolkit->setRoutes($routes);

        $dispatcher = new lmbRoutesRequestDispatcher(
            $path_offset = '/app',
            $base_path = 'https://example.com/app/'
        );

        $uri = new lmbUri('https://example.com/app/news/admin_display');
        $this->request = $this->request->withUri($uri);
        $result = $dispatcher->dispatch($this->request);

        $this->assertEquals('news', $result['controller']);
        $this->assertEquals('admin_display', $result['action']);
    }
}
