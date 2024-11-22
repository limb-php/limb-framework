<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\filter_chain\src;

use Psr\Http\Message\RequestInterface;

/**
 * Interface for filter classes to be used with lmbFilterChain
 *
 * @version $Id: lmbInterceptingFilterInterface.php 7486 2009-01-26 19:13:20Z
 * @package filter_chain
 */
interface lmbInterceptingFilterInterface
{
    /**
     * Runs the filter.
     * Filters should decide whether to pass control to the next filter in the chain or not.
     * @param $filter_chain lmbInterceptingFilterInterface
     * @param $request RequestInterface
     * @param $callback \Closure|null
     * @return mixed
     * @see lmbFilterChain::next()
     *
     */
    function run($filter_chain, $request, $callback = null);
}
