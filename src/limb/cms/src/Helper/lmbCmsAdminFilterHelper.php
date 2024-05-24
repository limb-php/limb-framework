<?php

namespace limb\cms\src\Helper;

use limb\toolkit\src\lmbToolkit;
use Psr\Http\Message\RequestInterface;

class lmbCmsAdminFilterHelper
{
    protected $request;
    protected $session;
    protected $filter_name;

    function __construct(string $filter_name, RequestInterface $request = null)
    {
        $this->filter_name = $filter_name;
        $this->request = $request ?? lmbToolkit::instance()->getRequest();
        $this->session = lmbToolkit::instance()->getSession();
    }

    function getParams(): array
    {
        return $this->session->get($this->filter_name, array());
    }

    function setParams(array $params)
    {
        $this->session->set($this->filter_name, $params);
    }

    function getFilter($param_name, $default = null)
    {
        $params = $this->getParams();

        if (isset($params[$param_name]))
            return $params[$param_name];

        return $default;
    }

    function setFilter($param_name, $default_value = null)
    {
        $params = $this->getParams();

        if (!$this->request->has($param_name)) {
            $value = $params[$param_name] ?? $default_value;

            if (is_string($value))
                $value = trim($value);

            $this->request->set($param_name, $value);
        } else {
            $value = $this->request->get($param_name);
        }

        $params[$param_name] = $value;

        $this->setParams($params);
    }

    function resetFilter($param_name)
    {
        $params = $this->getParams();

        if (isset($params[$param_name])) {
            unset($params[$param_name]);

            $this->setParams($params);
        }
    }

    function reset()
    {
        $this->session->set($this->filter_name, array());
    }
}
