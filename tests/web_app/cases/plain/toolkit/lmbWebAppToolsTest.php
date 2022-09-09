<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\web_app\src\request\lmbRoutes;
use limb\web_app\src\controller\LmbController;
use limb\core\src\lmbSet;
use limb\core\src\lmbEnv;
use limb\toolkit\src\lmbToolkit;
use limb\fs\src\lmbFs;
use limb\core\src\lmbObject;
use limb\core\src\exception\lmbException;

class lmbWebAppToolsTest extends TestCase
{
  function setUp()
  {
    lmbToolkit::save();
  }

  function tearDown()
  {
    lmbToolkit::restore();
  }

  function testToRouteUrl()
  {
    $routes_dataspace = new lmbSet();
    $config_array = array(array('path' => '/:controller/:action',
                                'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config_array);

    $toolkit = lmbToolkit :: merge(new lmbWebAppTools());
    $toolkit->setRoutes($routes);

    $to_url_params = array('controller' => 'news', 'action' => 'archive');
    $this->assertEqual($toolkit->getRoutesUrl($to_url_params), LIMB_HTTP_GATEWAY_PATH . ltrim($routes->toUrl($to_url_params), '/'));
  }

  function testToRouteUrlSkipController()
  {
    $routes_dataspace = new lmbSet();
    $config_array = array(array('path' => '/news/:action',
                                'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config_array);

    $toolkit = lmbToolkit :: merge(new lmbWebAppTools());
    $toolkit->setRoutes($routes);
    $toolkit->setDispatchedController(new lmbController());

    $to_url_params = array('action' => 'archive');
    $this->assertEqual($toolkit->getRoutesUrl($to_url_params, null, $skip_controller = true),
                       LIMB_HTTP_GATEWAY_PATH . 'news/archive');
  }

  function testIsWebAppDebugEnabled()
  {
    $toolkit = lmbToolkit :: merge(new lmbWebAppTools());

    $this->assertFalse($toolkit->isWebAppDebugEnabled());

    lmbEnv::set('LIMB_APP_MODE', 'devel');
    $this->assertTrue($toolkit->isWebAppDebugEnabled());

    $toolkit->setConf('common', new lmbObject(array('debug_enabled' => true)));
    $this->assertTrue($toolkit->isWebAppDebugEnabled());

    $toolkit->setConf('common', new lmbObject(array('debug_enabled' => false)));
    $this->assertFalse($toolkit->isWebAppDebugEnabled());
  }

  function testAddVersionToUrl()
  {
    $toolkit = lmbToolkit :: merge(new lmbWebAppTools());
    lmbEnv::set('LIMB_DOCUMENT_ROOT', null);
    try 
    {
      $toolkit->addVersionToUrl('js/main.js');
      $this->assertTrue(false);
    } catch (lmbException $e)  {
      $this->assertTrue(true);
    }

    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR').'/www');
    lmbFs::rm(lmbEnv::get('LIMB_DOCUMENT_ROOT').'/js/main.js');

    try 
    {
      $toolkit->addVersionToUrl('js/main.js');
      $this->assertTrue(false);
    } catch (lmbException $e)  {
      $this->assertTrue(true);
    }

    lmbFs :: safeWrite(lmbEnv::get('LIMB_DOCUMENT_ROOT').'/js/main.js', '(function() {})()');
    $url = $toolkit->addVersionToUrl('js/main.js');
    $url_abs = $toolkit->addVersionToUrl(lmbEnv::get('LIMB_HTTP_BASE_PATH') . 'js/main.js');
    $this->assertEqual($url, $url_abs);
  }

  function testAddVersionToUrl_Safe()
  {
    $toolkit = lmbToolkit :: merge(new lmbWebAppTools());
    lmbEnv::set('LIMB_DOCUMENT_ROOT', null);
    try 
    {
      $url = $toolkit->addVersionToUrl('js/main.js', true);
      $this->assertEqual($url, 'js/main.js?00');
      $this->assertTrue(true);
    } catch (lmbException $e)  {
      $this->assertTrue(false);
    }

    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR').'/www');
    lmbFs::rm(lmbEnv::get('LIMB_DOCUMENT_ROOT').'/js/main.js');

    try 
    {
      $url = $toolkit->addVersionToUrl('js/main.js', true);
      $this->assertEqual('js/main.js?00', $url);
      $this->assertTrue(true);
    } catch (lmbException $e)  {
      $this->assertTrue(false);
    }
  }
}
