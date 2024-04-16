<?php

namespace limb\web_app\src\request;

use limb\dbal\src\filter\lmbAutoDbTransactionFilter;
use limb\filter_chain\src\lmbFilterChain;
use limb\core\src\lmbHandle;
use limb\core\src\lmbEnv;
use limb\web_app\src\filter\lmbRequestDispatchingFilter;
use limb\web_app\src\filter\lmbSessionStartupFilter;

class lmbMiddleware extends lmbFilterChain
{
    protected $pre_dispatch_filters = array();
    protected $pre_action_filters = array();
    protected $default_controller_name = null;
    protected $request_dispatcher = null;

    static function create()
    {
        return new static();
    }

    function setDefaultControllerName($name)
    {
        $this->default_controller_name = $name;

        return $this;
    }

    function getRequestDispatcher()
    {
        if (!$this->request_dispatcher) {
            $this->request_dispatcher = new lmbRequestDispatchingFilter($this->default_controller_name);
        }

        return $this->request_dispatcher;
    }

    function setRequestDispatcher($dispatcher)
    {
        $this->request_dispatcher = $dispatcher;
    }

    function addPreDispatchFilter($filter)
    {
        $this->pre_dispatch_filters[] = $filter;
    }

    function addPreActionFilter($filter)
    {
        $this->pre_action_filters[] = $filter;
    }

    protected function _addFilters($filters)
    {
        foreach ($filters as $filter)
            $this->registerFilter($filter);
    }

    protected function _registerFilters(): void
    {
        $this->registerFilter(new lmbHandle(lmbSessionStartupFilter::class,
            lmbEnv::get('LIMB_SESSION_DRIVER'), lmbEnv::get('LIMB_SESSION_MAX_LIFE_TIME')));

        $this->registerFilter(new lmbHandle(lmbAutoDbTransactionFilter::class));

        $this->_addFilters($this->pre_dispatch_filters);

        $this->registerFilter($this->getRequestDispatcher());

        $this->_addFilters($this->pre_action_filters);
    }

    function process($request, $callback = null): \limb\net\src\lmbHttpResponse
    {
        $this->_registerFilters();

        return parent::process($request, $callback);
    }
}
