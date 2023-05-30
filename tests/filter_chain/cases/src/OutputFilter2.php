<?php

namespace tests\filter_chain\cases\src;

use limb\net\src\lmbHttpResponse;

class OutputFilter2
{
    function run($fc, $request, $response): lmbHttpResponse
    {
        echo '<filter2>';
        $response = $fc->next($request, $response);
        echo '</filter2>';

        return $response;
    }
}