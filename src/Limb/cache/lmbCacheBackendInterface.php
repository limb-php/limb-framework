<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\cache;

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
