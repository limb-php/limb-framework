<?php

namespace Tests\filter_chain\cases\src;

class OutputFilter2
{
    function run($fc, $request, $callback = null)
    {
        echo '<filter2>';
        $response = $fc->next($request, $callback);
        echo '</filter2>';

        return $response;
    }
}