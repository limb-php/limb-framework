<?php

namespace Tests\filter_chain\cases\src;

use limb\net\src\lmbHttpResponse;

class OutputFilter3
{
    function run($fc, $request, $callback = null)
    {
        echo '<filter3>';
        $response = $fc->next($request, $callback);
        echo '</filter3>';

        return $response;
    }
}