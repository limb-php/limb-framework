<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbTimingFilter.
 *
 * @package web_app
 * @version $Id: lmbTimingFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbTimingFilter implements lmbInterceptingFilterInterface
{
    public function run($filter_chain, $request = null, $callback = null)
    {
        $start_time = microtime(true);

        $response = $filter_chain->next($request, $callback);

        $logger = lmbToolkit::instance()->getLog('debug');
        $logger->debug($this->_getStat($request, $start_time));

        return $response;
    }

    function _getStat($request, $start_time): string
    {
        $mem_usage = memory_get_usage();
        $peak_mem_usage = memory_get_peak_usage();

        $time = date('Y.m.d H:i:s');
        $gentime = round(microtime(true) - $start_time, 4);

        $host = $request->getUri()->getHost();
        $path = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        $method = $request->getMethod();
        $agent = $request->getHeaderLine('User-Agent');

        return "$time: $method $host $path $query [$agent], memory: $mem_usage ($peak_mem_usage), time: $gentime s" . PHP_EOL;
    }
}
