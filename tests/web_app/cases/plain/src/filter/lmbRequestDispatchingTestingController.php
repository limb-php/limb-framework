<?php

namespace tests\web_app\cases\plain\src\filter;

use limb\web_app\src\Controllers\LmbController;

class lmbRequestDispatchingTestingController extends LmbController
{
    function __construct($name)
    {
        $this->name = $name;
        parent::__construct();
    }

    function doDisplay()
    {
    }
}
