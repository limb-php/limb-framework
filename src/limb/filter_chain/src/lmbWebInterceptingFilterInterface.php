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
interface lmbWebInterceptingFilterInterface
{
  /**
   * Runs the filter.
   * Filters should decide whether to pass control to the next filter in the chain or not.
   * @param $filter_chain lmbWebFilterChain
   * @param $request \limb\net\src\lmbHttpRequest
   * @param $response \limb\net\src\lmbHttpResponse
   * @return \limb\net\src\lmbHttpResponse
   * @see lmbFilterChain::next()
   *
   */
  function run($filter_chain, $request, $response);
}