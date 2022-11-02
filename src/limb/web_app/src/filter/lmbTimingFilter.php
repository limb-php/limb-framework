<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;

/**
 * class lmbTimingFilter.
 *
 * @package web_app
 * @version $Id: lmbTimingFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbTimingFilter implements lmbInterceptingFilterInterface
{
  public function run($filter_chain, $request = null, $response = null)
  {
    $start_time = microtime(true);

    $response = $filter_chain->next($request, $response);

    echo '<small>' . round(microtime(true) - $start_time, 2) . '</small>';

    return $response;
  }
}
