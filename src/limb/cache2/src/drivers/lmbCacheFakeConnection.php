<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html*/
namespace limb\cache2\src\drivers;

use limb\cache2\src\drivers\lmbCacheAbstractConnection;

/**
 * class lmbCacheFakeConnection.
 *
 * @package cache2
 * @version $Id$
 */
class lmbCacheFakeConnection extends lmbCacheAbstractConnection
{
  function __construct($dsn)
  {
  }

  function getType()
  {
    return 'fake';
  }

  function add($key, $value, $ttl = false)
  {
    return true;
  }

  function set($key, $value, $ttl = false)
  {
    return true;
  }

  function get($key)
  {
    return NULL;
  }

  function delete($key)
  {
    return true;
  }

  function flush()
  {
  }
}
