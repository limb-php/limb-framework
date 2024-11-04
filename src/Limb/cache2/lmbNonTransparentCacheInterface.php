<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache2;

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
