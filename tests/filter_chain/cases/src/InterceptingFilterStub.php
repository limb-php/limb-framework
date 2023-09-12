<?php

namespace Tests\filter_chain\cases\src;

class InterceptingFilterStub
{
    var $captured = array();
    var $run = false;

    function run($fc, $request, $callback = null)
    {
        $this->run = true;
        $this->captured['filter_chain'] = $fc;

        return $fc->next($request, $callback);
    }
}