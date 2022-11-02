<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\filter;

use limb\core\src\lmbEnv;
use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\session\src\lmbSessionNativeStorage;
use limb\session\src\lmbSessionDbStorage;
use limb\session\src\lmbSessionMemcacheStorage;
use limb\session\src\lmbSessionMemcachedStorage;
use limb\toolkit\src\lmbToolkit;

/**
 * lmbSessionStartupFilter installs session storage driver and starts session.
 *
 * What session storage driver will be used is depend on {@link LIMB_USE_DRIVER} constant value.
 * If LIMB_USE_DRIVER has FALSE value or not defined - native file based session storage will be used.
 * Otherwise database storage driver will be installed.
 * @see lmbSessionNativeStorage
 * @see lmbSessionMemcacheStorage
 * @see lmbSessionMemcachedStorage
 * @see lmbSessionDbStorage
 *
 * @version $Id: lmbSessionStartupFilter.php 7486 2009-01-26 19:13:20Z
 * @package web_app
 */
class lmbSessionStartupFilter implements lmbInterceptingFilterInterface
{
  protected $session_type;
  protected $session_lifetime;

  /**
   * @uses LIMB_SESSION_DRIVER, LIMB_SESSION_MAX_LIFE_TIME
   */
  function __construct($session_type = null, $session_lifetime = null)
  {
      $this->session_type = $session_type ?? (lmbEnv::get('LIMB_SESSION_DRIVER') ?? 'native');
      $this->session_lifetime = $session_lifetime ?? (lmbEnv::get('LIMB_SESSION_MAX_LIFE_TIME') ?? 0);
  }

  /**
   * @see lmbInterceptingFilter::run()
   */
  function run($filter_chain, $request = null, $response = null)
  {
    if($this->session_type == 'db')
      $storage =  $this->_createDBSessionStorage($this->session_lifetime);
    elseif($this->session_type == 'memcache')
      $storage =  $this->_createMemcacheSessionStorage($this->session_lifetime);
    elseif($this->session_type == 'memcached')
      $storage =  $this->_createMemcachedSessionStorage($this->session_lifetime);
    else
      $storage =  $this->_createNativeSessionStorage();

    $session = lmbToolkit::instance()->getSession();
    $session->start($storage);

    $response = $filter_chain->next($request, $response);

    $session->close();

    return $response;
  }

  protected function _createNativeSessionStorage()
  {
    return new lmbSessionNativeStorage();
  }

  protected function _createMemcachedSessionStorage($lifetime)
  {
    $memcached_conf = lmbToolkit::instance()->getConf('memcached');
    return new lmbSessionMemcachedStorage($memcached_conf['host'] ?? 'localhost', $memcached_conf['port'] ?? '11211', $lifetime);
  }

  protected function _createMemcacheSessionStorage($lifetime)
  {
    $memcache_conf = lmbToolkit::instance()->getConf('memcache');
    return new lmbSessionMemcacheStorage($memcache_conf['host'] ?? 'localhost', $memcache_conf['port'] ?? '11211', $lifetime);
  }

  /**
   * Creates object of {@link lmbSessionDbStorage} class.
   * If constant LIMB_SESSION_MAX_LIFE_TIME is defined passed it's value as session max life time
   * @see lmbInterceptingFilter::run()
   * @uses LIMB_SESSION_MAX_LIFE_TIME
   */
  protected function _createDBSessionStorage($lifetime)
  {
    $db_connection = lmbToolkit::instance()->getDefaultDbConnection();
    return new lmbSessionDbStorage($db_connection, $lifetime);
  }
}
