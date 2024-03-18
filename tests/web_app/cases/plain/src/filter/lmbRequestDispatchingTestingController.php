<?php

namespace tests\web_app\cases\plain\src\filter;

use limb\web_app\src\Controllers\LmbController;

class lmbRequestDispatchingTestingController extends LmbController
{
    protected $request;

    function __construct($name)
    {
        $this->name = $name;
        parent::__construct();
    }

    function doDisplay($request)
    {
        $this->request = $request;
    }

    function getRequest()
    {
        return $this->request;
    }
}
