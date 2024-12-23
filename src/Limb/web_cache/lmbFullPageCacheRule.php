<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache;

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
