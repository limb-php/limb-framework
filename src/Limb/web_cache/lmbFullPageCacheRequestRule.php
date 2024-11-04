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
 * class lmbFullPageCacheRequestRule.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheRequestRule.php 7686 2009-03-04 19:57:12Z
 */
class lmbFullPageCacheRequestRule
{
    protected $get;
    protected $post;

    function __construct($get = null, $post = null)
    {
        $this->get = $get;
        $this->post = $post;
    }

    function isSatisfiedBy($request): bool
    {
        /** @var \limb\net\src\lmbHttpRequest $http_request */
        $http_request = $request->getHttpRequest();

        if (!$this->_matches($this->get, $http_request->getGet()))
            return false;

        if (!$this->_matches($this->post, $http_request->getPost()))
            return false;

        return true;
    }

    function _matches($expected, $variable): bool
    {
        if (is_array($expected)) {
            foreach ($expected as $key => $value) {
                if (!isset($variable[$key]) || ($value != '*' && $value != $variable[$key]))
                    return false;
            }
        } elseif ($expected == '!' && !empty($variable))
            return false;
        elseif ($expected == '*' && empty($variable))
            return false;

        return true;
    }
}
