<?php

namespace limb\web_app\src\request;

use limb\dbal\src\filter\lmbAutoDbTransactionFilter;
use limb\filter_chain\src\lmbFilterChain;
use limb\web_app\src\filter\lmbSessionStartupFilter;
use Psr\Http\Message\ResponseInterface;

class lmbMiddlewarePipe extends lmbFilterChain
{
    private bool $filtersRegistered = false;

    static function create(): static
    {
        return new static();
    }

    protected function _registerFilters(): void
    {
        $this->registerFilter(lmbSessionStartupFilter::class);

        $this->registerFilter(lmbAutoDbTransactionFilter::class);
    }

    function handle($request, $callback = null): ResponseInterface
    {
        if (!$this->filtersRegistered) {
            $this->_registerFilters();
            $this->filtersRegistered = true;
        }

        return parent::handle($request, $callback);
    }
}
