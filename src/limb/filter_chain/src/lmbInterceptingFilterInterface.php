<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\filter_chain\src;

use Closure;
use Psr\Http\Message\RequestInterface;

/**
 * Contract for filters used with {@see lmbFilterChain}.
 *
 * @package filter_chain
 */
interface lmbInterceptingFilterInterface
{
    /**
     * Runs the filter. Implementations decide whether to continue the
     * pipeline by calling $filter_chain->next($request, $callback).
     *
     * @return mixed
     * @see lmbFilterChain::next()
     */
    public function run(lmbInterceptingFilterInterface $filter_chain, RequestInterface $request, ?Closure $callback = null);
}
