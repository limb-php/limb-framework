<?php

namespace tests\filter_chain\cases\src;

class InterceptingFilterStub
{
    public $captured = [];

    public $run = false;

    function run($fc, $request, $callback = null)
    {
        $this->run = true;
        $this->captured['filter_chain'] = $fc;

        return $fc->next($request, $callback);
    }
}