<?php

namespace tests\filter_chain\cases\src;

use limb\net\src\lmbHttpResponse;

class OutputFilter1
{
    function run($fc, $request, $response): lmbHttpResponse
    {
        echo '<filter1>';
        $response = $fc->next($request, $response);
        echo '</filter1>';

        return $response;
    }
}