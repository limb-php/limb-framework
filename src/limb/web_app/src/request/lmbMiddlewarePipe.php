<?php

namespace limb\web_app\src\request;

use limb\dbal\src\filter\lmbAutoDbTransactionFilter;
use limb\filter_chain\src\lmbFilterChain;
use limb\core\src\lmbHandle;
use limb\core\src\lmbEnv;
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

    function process($request, $callback = null): \limb\net\src\lmbHttpResponse
    {
        $this->_registerFilters();

        return parent::process($request, $callback);
    }
}
