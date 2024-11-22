<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\request;

require_once dirname(__FILE__) . '/../../init.inc.php';

use PHPUnit\Framework\TestCase;
use limb\web_app\src\request\lmbRoutes;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;
use tests\web_app\cases\plain\src\Controllers\Api\Admin\FreeController;
use tests\web_app\cases\plain\src\Controllers\Api\ApiTestingController;
use tests\web_app\cases\plain\src\Controllers\SecondTestingController;

class lmbRoutesToUrlTest extends TestCase
{
    function setUp(): void
    {
        lmbToolkit::save();
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

            'api_articles_action_id' => array(
                'prefix' => 'api',
                'path' => '/articles/:action/:id',
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
        $this->assertEquals('/api/articles/id/10', $routes->toUrl(['action' => 'id', 'id' => 10], 'api_articles_action_id'));
    }

    function testToUrlWithPrefixNamespace()
    {
        $config = array(
            'api_articles' => array(
                'prefix' => 'api',
                'path' => '/:controller',
                'defaults' => array(
                    'namespace' => 'Tests\web_app\cases\plain\src\Controllers\Api',
                    'action' => 'display'
                )
            ),

            'api_admin_articles' => array(
                'prefix' => 'api/admin',
                'path' => '/:controller',
                'defaults' => array(
                    'namespace' => 'Tests\web_app\cases\plain\src\Controllers\Api\Admin',
                    'action' => 'display'
                )
            ),

            'articles' => array(
                'path' => '/articles',
                'defaults' => array(
                    'controller' => 'second_testing',
                    'action' => 'display'
                )
            ),
        );

        $routes = new lmbRoutes($config);

        $controller = ApiTestingController::class;
        $this->assertEquals('/api/api_testing', $routes->toUrl(['controller' => $controller]));

        $controller2 = SecondTestingController::class;
        $this->assertEquals('/articles', $routes->toUrl(['controller' => $controller2], 'articles'));

        $controller3 = new FreeController();
        $this->assertEquals('/api/admin/free', $routes->toUrl(['controller' => $controller3]));
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
