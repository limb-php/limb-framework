<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache2\src\drivers;

/**
 * interface lmbNonTransparentCache.
 *
 * @package cache
 * @version $Id$
 */
interface lmbCacheConnectionInterface
{
    function add($key, $value, $ttl = false);

    function set($key, $value, $ttl = false);

    function get($key);

    function delete($key);

    function flush();
}
