<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_cache\src;

/**
 * class lmbFullPageCacheUriPathRule.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheUriPathRule.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheUriPathRule
{
    protected $path_regex;
    protected $offset;
    protected $dont_negate = true;

    function __construct($path_regex, $dont_negate = true)
    {
        $this->path_regex = $path_regex;
        $this->dont_negate = $dont_negate;
    }

    function useOffset($offset)
    {
        $this->offset = $offset;
    }

    function isSatisfiedBy($request)
    {
        $path = $request->getHttpRequest()->getUri()->getPath();
        $path = $this->_applyOffset($path);

        if ($this->dont_negate)
            return preg_match($this->path_regex, $path);
        else
            return !preg_match($this->path_regex, $path);
    }

    function _applyOffset($path)
    {
        if (!$this->offset)
            return $path;

        $pieces = explode($this->offset, $path, 2);
        return isset($pieces[1]) ? $pieces[1] : $pieces[0];
    }
}
