<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache\src;

/**
 * class lmbFullPageCacheRuleset.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheRuleset.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheRuleset
{
    protected $rules = array();
    protected $type = true;

    function __construct($type = true)
    {
        $this->type = $type;
    }

    function setType($type)
    {
        return $this->type = $type;
    }

    function isAllow()
    {
        return $this->type == true;
    }

    function isDeny()
    {
        return $this->type == false;
    }

    function addRule($rule)
    {
        $this->rules[] = $rule;
    }

    function isSatisfiedBy($request)
    {
        foreach ($this->rules as $rule) {
            if (!$rule->isSatisfiedBy($request))
                return false;
        }

        return true;
    }
}
