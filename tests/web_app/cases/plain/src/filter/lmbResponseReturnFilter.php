<?php

namespace tests\web_app\cases\plain\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;

class lmbResponseReturnFilter implements lmbInterceptingFilterInterface
{
    function run($filter_chain, $request = null, $callback = null)
    {
        return lmbToolkit::instance()->getResponse();
    }
}
