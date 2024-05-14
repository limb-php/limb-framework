<?php

namespace tests\filter_chain\cases\src;

class OutputFilter1
{
    function run($fc, $request, $callback = null)
    {
        echo '<filter1>';
        $response = $fc->next($request, $callback);
        echo '</filter1>';

        return $response;
    }

    function handle($fc, $request, $callback = null)
    {
        return $this->run($fc, $request, $callback);
    }
}