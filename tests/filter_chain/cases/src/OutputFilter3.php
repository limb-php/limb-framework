<?php

namespace filter_chain\cases\src;

use limb\net\src\lmbHttpResponse;

class OutputFilter3
{
    function run($fc, $request, $response): lmbHttpResponse
    {
        echo '<filter3>';
        $response = $fc->next($request, $response);
        echo '</filter3>';

        return $response;
    }
}