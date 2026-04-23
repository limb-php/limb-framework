<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\filter_chain\src;

/**
 *  Legacy intercepting-filter chain without an HTTP request/callback.
 *
 *  Filters call $chain->next() to continue the pipeline. For PSR-style
 *  request/response middleware use {@see lmbFilterChain} instead.
 *
 *  Usage:
 *  <code>
 *  $chain = new lmbChain();
 *  $chain->registerFilter(new A());
 *  $chain->registerFilter(new B());
 *  $chain->process();
 *  </code>
 *
 * @package filter_chain
 */
class lmbChain implements lmbChainInterface
{
    /**
     * @var array registered filters (or filter handles (see {@link lmbHandle}))
     */
    protected $filters = array();
    /**
     * @var integer Index of the current active filter while running the chain
     */
    protected $counter = -1;

    function __construct()
    {
    }

    /**
     * Registers filter (or handle on a filter) in the chain.
     *
     * @return void
     */
    function registerFilter($filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Returns registered filters
     *
     * @return array
     */
    function getFilters()
    {
        return $this->filters;
    }

    /**
     * Runs next filter in the chain.
     *
     * @return void
     */
    function next()
    {
        $this->counter++;

        if (isset($this->filters[$this->counter])) {
            $this->filters[$this->counter]->run($this);
        }
    }

    /**
     * Executes the chain
     *
     * @return void
     */
    function process()
    {
        $this->counter = -1;
        $this->next();
    }

    /**
     * Implements lmbInterceptingFilter interface.
     * Filter chain can be an intercepting filter.
     *
     * @param object $filter_chain Filter chain instance
     * @return void
     */
    function run($filter_chain, ...$params)
    {
        $this->process();
        $filter_chain->next();
    }
}
