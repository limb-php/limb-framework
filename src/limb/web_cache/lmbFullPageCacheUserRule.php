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
 * class lmbFullPageCacheUserRule.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheUserRule.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheUserRule
{
    protected $user_groups = [];

    function __construct($user_groups)
    {
        $this->user_groups = $user_groups;
    }

    function isSatisfiedBy($request): bool
    {
        $user = $request->getUser();

        $positive_groups = [];
        $negative_groups = [];

        foreach ($this->user_groups as $group) {
            if ($group[0] == '!')
                $negative_groups[] = substr($group, 1);
            else
                $positive_groups[] = $group;
        }

        $res = true;

        if ($positive_groups)
            $res = (array_intersect($positive_groups, $user->getGroups()) == $positive_groups);

        if ($res && $negative_groups)
            $res &= !(array_intersect($negative_groups, $user->getGroups()) == $negative_groups);

        return (bool)$res;
    }
}
