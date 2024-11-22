<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache\src;

/**
 * class lmbFullPageCachePolicy.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCachePolicy.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCachePolicy
{
    protected $rulesets;

    function __construct()
    {
        $this->reset();
    }

    function reset()
    {
        $this->rulesets = array();
    }

    function readRules($reader)
    {
        $this->reset();

        foreach ($reader->getRulesets() as $rule)
            $this->addRuleset($rule);
    }

    function addRuleset($rule)
    {
        $this->rulesets[] = $rule;
    }

    function findRuleset($request)
    {
        foreach ($this->rulesets as $rule) {
            if ($rule->isSatisfiedBy($request))
                return $rule;
        }
    }

    function getRules()
    {
        return $this->rulesets;
    }
}
