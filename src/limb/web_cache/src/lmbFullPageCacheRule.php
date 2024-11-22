<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache\src;

/**
 * class lmbFullPageCacheRule.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheRule.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheRule
{
    function isSatisfiedBy($request)
    {
        return true;
    }
}
