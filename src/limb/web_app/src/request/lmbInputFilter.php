<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\request;

/**
 * class lmbInputFilter.
 *
 * @package web_app
 * @version $Id: lmbInputFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbInputFilter
{
    protected $rules = array();

    function addRule($rule)
    {
        $this->rules[] = $rule;
    }

    function filter($input)
    {
        $result = $input;

        foreach (array_keys($this->rules) as $key)
            $result = $this->rules[$key]->apply($result);

        return $result;
    }
}
