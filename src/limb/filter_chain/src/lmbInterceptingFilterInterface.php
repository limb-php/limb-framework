<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\filter_chain\src;

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
   * @param $filter_chain lmbFilterChain
   * @param $request \limb\net\src\lmbHttpRequest
   * @param $callback \Closure|null
   * @return mixed
   * @see lmbFilterChain::next()
   *
   */
  function run($filter_chain, $request, $callback = null);
}
