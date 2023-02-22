<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\cache\src;

/**
 * class lmbCacheMemcachedBackend.
 *
 * @package cache
 * @version $Id: lmbCacheFilePersister.php 6243 2007-08-29 11:53:10Z
 */
class lmbCacheMemcachedBackend implements lmbCacheBackendInterface
{
  protected $_memcache;

  function __construct($host = 'localhost', $port = '11211')
  {
    $this->_memcache = new \Memcached();
    $this->_memcache->addServer($host, $port);
  }

  function add($key, $value, $params = array())
  {
    return $this->_memcache->add($key, $value, $this->_getTtl($params));
  }

  function set($key, $value, $params = array())
  {
    return $this->_memcache->set($key, $value, $this->_getTtl($params));
  }

  function get($key, $params = array())
  {
    if (false === ($value = $this->_memcache->get($key)))
      return false;

    return $value;
  }

  function delete($key, $params = array())
  {
    $this->_memcache->delete($key);
  }

  function flush()
  {
    $this->_memcache->flush();
  }

  function stat($params = array())
  {
    return $this->_memcache->getStats(
      $params['cache_type'] ?? null
    );
  }

  protected function _getTtl($params)
  {
    if (!isset($params['ttl']))
      $params['ttl'] = 0;

    return $params['ttl'];
  }
}

