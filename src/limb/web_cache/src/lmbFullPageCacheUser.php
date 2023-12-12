<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache\src;

/**
 * class lmbFullPageCacheUser.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheUser.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheUser
{
    protected $groups;

    function __construct($groups = array())
    {
        $this->groups = $groups;
    }

    function getGroups()
    {
        return $this->groups;
    }
}
