<?php
/*
 * Limb PHP Framework
 *
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
    function flush();

    function stat($params = []);
}
