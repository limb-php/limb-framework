<?php

namespace tests\filter_chain\cases\src;

class OutputFilter3
{
    function run($fc, $request, $callback = null)
    {
        echo '<filter3>';
        $response = $fc->next($request, $callback);
        echo '</filter3>';

        return $response;
    }

    function handle($fc, $request, $callback = null)
    {
        return $this->run($fc, $request, $callback);
    }
}