<?php

namespace tests\web_app\cases\plain\src\Controllers\Api;

use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\Controllers\LmbController;

class ApiTestingController extends LmbController
{
    function doDisplay()
    {
        return 'foo';
    }
}
