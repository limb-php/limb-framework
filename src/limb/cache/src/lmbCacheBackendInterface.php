<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cache\src;

/**
 * interface lmbCacheBackend.
 *
 * @package cache
 * @version $Id$
 */
interface lmbCacheBackendInterface
{
    function add($key, $value, $params = array(), $ttl = null);

    function set($key, $value, $params = array(), $ttl = null);

    function get($key, $params = array());

    function delete($key, $params = array());

    function flush();

    function stat($params = array());
}
