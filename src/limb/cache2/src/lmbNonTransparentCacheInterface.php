<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache2\src;

/**
 * interface lmbNonTransparentCacheInterface.
 *
 * @package cache
 * @version $Id$
 */
interface lmbNonTransparentCacheInterface
{
    function add($key, $value, $ttl = false);

    function set($key, $value, $ttl = false);

    function get($key);

    function delete($key);

    function flush();
}
