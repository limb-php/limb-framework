<?php

namespace tests\filter_chain\cases\src;

use limb\filter_chain\src\lmbInterceptingFilterInterface;

class OutputFilter2 implements lmbInterceptingFilterInterface
{
    function run(lmbInterceptingFilterInterface $filter_chain, $request, $callback = null)
    {
        return '<filter2>' . $filter_chain->next($request, $callback) . '</filter2>';
    }

}