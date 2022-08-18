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
 * @version $Id: lmbTimingFilter.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbTimingFilter implements lmbInterceptingFilterInterface
{
  public function run($filter_chain)
  {
    $start_time = microtime(true);

    $filter_chain->next();

    echo '<small>' . round(microtime(true) - $start_time, 2) . '</small>';
  }
}


