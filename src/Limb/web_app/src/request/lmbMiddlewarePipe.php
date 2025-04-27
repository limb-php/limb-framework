<?php

namespace limb\web_app\src\request;

use limb\Dbal\Filter\lmbAutoDbTransactionFilter;
use limb\FilterChain\lmbFilterChain;
use limb\Net\lmbHttpResponse;
use limb\web_app\src\filter\lmbSessionStartupFilter;

class lmbMiddlewarePipe extends lmbFilterChain
{
    static function create(): static
    {
        return new static();
    }

    protected function _registerFilters(): void
    {
        $this->registerFilter(lmbSessionStartupFilter::class);

        $this->registerFilter(lmbAutoDbTransactionFilter::class);
    }

    function process($request, $callback = null): lmbHttpResponse
    {
        $this->_registerFilters();

        return parent::process($request, $callback);
    }
}
