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
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbResponseTransactionFilter.
 *
 * @package web_app
 * @version $Id: lmbResponseTransactionFilter.php 7486 2009-01-26 19:13:20Z
 * @deprecated
 */
class lmbResponseTransactionFilter implements lmbInterceptingFilterInterface
{
    function run($filter_chain, $request = null, $callback = null)
    {
        $response = $filter_chain->next($request, $callback);

        $response->send();

        return $response;
    }
}
