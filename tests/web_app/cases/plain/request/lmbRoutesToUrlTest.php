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
use limb\web_app\src\request\lmbRoutes;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;
use Tests\web_app\cases\plain\src\Controllers\Api\ApiTestingController;

class lmbRoutesToUrlTest extends TestCase
{
    function setUp(): void
    {
        $toolkit = lmbToolkit::save();
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function testToUrl()
    {
        $config = array(
            'blog' => array(
                'path' => '/blog',
                'defaults' => array(
                    'controller' => 'Blog',
                    'action' => 'display')
            ),
            'news' => array(
                'path' => '/news',
                'defaults' => array(
                    'controller' => 'Newsline',
                    'action' => 'display')
            ),
        );

        $routes = new lmbRoutes($config);
        $this->assertEquals('/blog', $routes->toUrl([], 'blog'));
        $this->assertEquals('/news', $routes->toUrl([], 'news'));
    }

    function testToUrlWithPrefix()
    {
        $config = array(
            'api_articles' => array(
                'prefix' => 'api',
                'path' => '/articles',
                'defaults' => array(
                    'controller' => 'Api\Articles',
                    'action' => 'display')
            ),

            'articles' => array(
                'path' => '/articles',
                'defaults' => array(
                    'controller' => 'Articles',
                    'action' => 'display')
            ),
        );

        $routes = new lmbRoutes($config);
        $this->assertEquals('/api/articles', $routes->toUrl([], 'api_articles'));
        $this->assertEquals('/articles', $routes->toUrl(['controller' => 'Articles']));
        $this->assertEquals('/api/articles', $routes->toUrl(['controller' => 'Api\Articles']));
        $this->assertEquals('/api/articles', $routes->toUrl([], 'api_articles'));
        $this->assertEquals('/api/articles', $routes->toUrl(['prefix' => 'api']));

    }

    function testToUrlWithPrefixNamespace()
    {
        $config = array(
            'api_articles' => array(
                'prefix' => 'api',
                'path' => '/:controller',
                'defaults' => array(
                    'namespace' => 'Tests\web_app\cases\plain\src\Controllers\Api',
                    'action' => 'display')
            ),

            'articles' => array(
                'path' => '/:controller',
                'defaults' => array(
                    'controller' => 'Articles',
                    'action' => 'display')
            ),
        );

        $routes = new lmbRoutes($config);
        $controller = ApiTestingController::class;
        $this->assertEquals('/api/articles', $routes->toUrl(['controller' => $controller]));
    }

    function testToUrlUseNamedParam()
    {
        $config = array('default' => array('path' => '/:controller/display',
            'defaults' => array('action' => 'display')));

        $routes = new lmbRoutes($config);
        $this->assertEquals('/news/display', $routes->toUrl(array('controller' => 'news'), 'default'));
    }

    function testToUrlWithSuffix()
    {
        $config = array(
            'default' => array(
                'path' => '/:controller/:action/:id:.html',
                'defaults' => array('action' => 'display')));

        $routes = new lmbRoutes($config);
        $this->assertEquals('/news/display/test.html', $routes->toUrl(array('controller' => 'news', 'action' => 'display', 'id' => 'test')));
    }

    function testToUrlApplyDefaultParamValue()
    {
        $config = array(
            'default' => array(
                'path' => '/:controller/:action',
                'defaults' => array('action' => 'display')));

        $routes = new lmbRoutes($config);
        $this->assertEquals('/news/', $routes->toUrl(array('controller' => 'news'), 'default'));
    }

    function testToUrlApplyDefaultParamValueWithNoParamsInPath()
    {
        $config = array(
            'default' => array(
                'path' => '/news/:action/:id',
                'defaults' => array('controller' => 'news', 'action' => 'display'))
        );

        $routes = new lmbRoutes($config);
        $this->assertEquals('/news/display/test', $routes->toUrl(array('controller' => 'news', 'action' => 'display', 'id' => 'test')));
    }

    function testThrowExceptionIfNotEnoughParams()
    {
        $config = array('default' => array('path' => '/:controller/:action'));

        $routes = new lmbRoutes($config);
        try {
            $routes->toUrl(array('controller' => 'news'), 'default');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testThrowExceptionIfNotFoundAnyMatchingRoute()
    {
        $config = array(
            'default' => array(
                'path' => '/:controller/:action',
                'defaults' => array('action' => 'display'))
        );

        $routes = new lmbRoutes($config);
        try {
            $routes->toUrl(array(), 'default');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testToUrlTryToGuessRoute()
    {
        $config = array('default' => array('path' => '/:controller/display',
            'defaults' => array('action' => 'display')),
            'full' => array('path' => '/:controller/:action',
                'defaults' => array('action' => 'display')));

        $routes = new lmbRoutes($config);
        $this->assertEquals('/news/archive', $routes->toUrl(array('controller' => 'news',
            'action' => 'archive')));
    }

    function testNoSuchRoute()
    {
        $config = array(
            'AdminPanel' =>
                array('path' => '/admin',
                    'defaults' => array('controller' => 'AdminPanel',
                        'action' => 'admin_display')),

            'EdPrograms' =>
                array('path' => '/admin/programs/:action',
                    'defaults' => array('controller' => 'EdPrograms',
                        'action' => 'admin_display')),

            'EdCourses' =>
                array('path' => '/admin/courses/:action',
                    'defaults' => array('controller' => 'EdCourses',
                        'action' => 'admin_display')),

        );

        $routes = new lmbRoutes($config);
        try {
            $routes->toUrl(array('action' => 'create'), 'Course');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testApplyUrlFilter()
    {
        $config = array('default' => array('path' => '/:controller/:action',
            'defaults' => array('action' => 'display'),
            'url_filter' => array($this, '_processUrlResult')));

        $routes = new lmbRoutes($config);
        $this->assertEquals('/admin/news/archive', $routes->toUrl(array('controller' => 'admin_news',
            'action' => 'archive')));
    }

    function _processUrlResult(&$path, $route)
    {
        $path = str_replace('/admin_', '/admin/', $path);
    }

    function testRemoveUnneededDefaultParamsFromUrl()
    {
        $config = array(
            'default' => array(
                'path' => '/users/:user/:controller/:action/:id/',
                'defaults' => array(
                    'user' => 'admin',
                    'controller' => 'blog',
                    'action' => 'display',
                    'id' => 0
                )
            )
        );

        $routes = new lmbRoutes($config);

        $this->assertEquals('/users/', $routes->toUrl(array()));
        $this->assertEquals('/users/bob/', $routes->toUrl(array('user' => 'bob')));
        $this->assertEquals('/users/', $routes->toUrl(array('user' => 'admin')));
        $this->assertEquals('/users/bob/blog/index/', $routes->toUrl(array('user' => 'bob', 'action' => 'index')));
        $this->assertEquals('/users/admin/article/', $routes->toUrl(array('controller' => 'article')));
        $this->assertEquals('/users/admin/article/display/5/', $routes->toUrl(array('controller' => 'article', 'id' => 5)));
        $this->assertEquals('/users/', $routes->toUrl(array('user' => 'admin', 'action' => 'display', 'id' => 0)));
        $this->assertEquals('/users/admin/blog/display/19/', $routes->toUrl(array('user' => 'admin', 'id' => 19)));
    }

    function testToUrlChecksRequirements()
    {
        $config = array(
            'default' => array(
                'path' => '/:controller/:action/',
                'requirements' => array(
                    'controller' => '/^blog$/',
                    'action' => '/^[a-z]+$/'
                )
            )
        );

        $routes = new lmbRoutes($config);

        $this->assertEquals('/blog/edit/', $routes->toUrl(array('controller' => 'blog', 'action' => 'edit')));

        try {
            $routes->toUrl(array('controller' => 'admin', 'action' => '123edit'));
            $routes->toUrl(array('controller' => 'zzz', 'action' => 'edit'));
            $routes->toUrl(array('controller' => 'blog', 'action' => '@#%'));
            $this->fail("Some routes do NOT match required params!");
        } catch (lmbException $e) {
            $this->assertMatchesRegularExpression('/route .* not found .*/i', $e->getMessage());
        }
    }
}
