<?php
/*
* Limb PHP Framework
*
* @license    LGPL http://www.gnu.org/copyleft/lesser.html*/

namespace limb\cache2\src\drivers;

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
        return false;
    }

    function delete($key)
    {
        return true;
    }

    function flush()
    {
    }
}
