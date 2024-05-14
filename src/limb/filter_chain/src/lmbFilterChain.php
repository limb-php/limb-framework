<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\filter_chain\src;

use limb\net\src\lmbHttpRequest;

/**
 *  lmbFilterChain is an implementation of InterceptingFilter design pattern.
 *
 *  lmbFilterChain contains registered filters and controls execution of the chain.
 *  Usually used as a FrontController in Limb based web applications (see web_app package)
 *
 *  lmbFilterChain can be an intercepting filter in its turn as well.
 *
 *  The best way to think about filters is as of a "russian nested doll", e.g:
 *  <code>
 *  // +-Filter A
 *  // | +-Filter B
 *  // | | +-Filter C
 *  // | | |_
 *  // | |_
 *  // |_
 *  </code>
 *  To achieve this sample structure you should write the following code:
 *  <code>
 *  $chain = new lmbFilterChain();
 *  $chain->registerFilter(new A());
 *  $chain->registerFilter(new B());
 *  $chain->registerFilter(new C());
 *  </code>
 *
 *  Remember, it's the filter that decides whether to pass control to the
 *  underlying filter, this is done by calling filter chain instance next()
 *  method.
 *
 *  Usage example:
 *  <code>
 *  //create new chain
 *  $chain = new limb\filter_chain\src\lmbFilterChain();
 *  //register filter object in the chain
 *  $chain->registerFilter(new MyFilter());
 *  //register a handle for a filter in the chain
 *  //in this case we can avoid PHP code parsing if
 *  //this filter won't be processed
 *  $chain->registerFilter(new lmbHandle('\namespace\MyFilter'));
 *  //executes the chain
 *  $chain->process();
 *  </code>
 *
 * @version $Id: lmbFilterChain.php 7486 2009-01-26 19:13:20Z
 * @package filter_chain
 */
class lmbFilterChain implements lmbInterceptingFilterInterface
{
    /**
     * @var array registered filters (or filter handles (see {@link lmbHandle}))
     */
    protected $filters = [];
    /**
     * @var integer Index of the current active filter while running the chain
     */
    protected $counter = -1;

    /**
     * Registers filter (or handle on a filter) in the chain.
     *
     * @return self
     */
    function registerFilter($filter, ...$agrs)
    {
        $this->filters[] = [$filter, $agrs];

        return $this;
    }

    /**
     * Returns registered filters
     *
     * @return array
     */
    function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Runs next filter in the chain.
     *
     * @return mixed
     */
    function next($request, $callback)
    {
        $this->counter++;

        if (isset($this->filters[$this->counter])) {
            [$filter, $agrs] = $this->filters[$this->counter];
            if(!is_object($filter)){
                $filter = new $filter(...$agrs);
            }

            /** @var $filter lmbInterceptingFilterInterface */
            //return $filter->handle($this, $request, $callback);
            return $filter->run($this, $request, $callback);
        }

        if (is_callable($callback))
            return $callback($request);

        return null;
    }

    /**
     * Executes the chain
     *
     * @return mixed
     */
    function process($request, $callback = null)
    {
        $this->counter = -1;

        return $this->next($request, $callback);
    }

    /**
     * Implements lmbInterceptingFilter interface.
     * Filter chain can be an intercepting filter.
     *
     * @param $filter_chain lmbFilterChain
     * @param $request lmbHttpRequest
     * @param $callback \Closure|null
     * @return mixed
     * @see lmbFilterChain::next()
     *
     */
    function run($filter_chain, $request, $callback = null)
    {
        $this->process($request, $callback);

        return $filter_chain->next($request, $callback);
    }

    function handle($filter_chain, $request, $callback = null)
    {
        return $this->run($filter_chain, $request, $callback);
    }
}
