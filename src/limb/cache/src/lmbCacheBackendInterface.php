<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache\src;

use Psr\SimpleCache\CacheInterface;

/**
 * interface lmbCacheBackend.
 *
 * @package cache
 * @version $Id$
 */
interface lmbCacheBackendInterface extends CacheInterface
{
    function add($key, $value, $ttl = null);

    function set($key, $value, $ttl = null);

    function get($key, $default = null);

    function delete($key);

    function flush();

    function stat($params = []);
}
