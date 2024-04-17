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

    protected function _addFilters($filters)
    {
        foreach ($filters as $filter)
            $this->registerFilter($filter);
    }

    protected function _registerFilters(): void
    {
        $this->registerFilter(new lmbHandle(lmbSessionStartupFilter::class,
            lmbEnv::get('LIMB_SESSION_DRIVER'), lmbEnv::get('LIMB_SESSION_MAX_LIFE_TIME'))
        );

        $this->registerFilter(new lmbHandle(lmbAutoDbTransactionFilter::class));
    }

    function process($request, $callback = null): \limb\net\src\lmbHttpResponse
    {
        $this->_registerFilters();

        return parent::process($request, $callback);
    }
}
