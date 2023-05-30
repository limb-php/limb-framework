<?php

namespace tests\filter_chain\cases\src;

use limb\net\src\lmbHttpResponse;

class InterceptingFilterStub
{
    var $captured = array();
    var $run = false;

    function run($fc, $request, $response): lmbHttpResponse
    {
        $this->run = true;
        $this->captured['filter_chain'] = $fc;

        $response = response();

        return $fc->next($request, $response);
    }
}