<?php

namespace tests\web_app\cases\plain\src\Controllers;

use limb\web_app\src\Controllers\LmbController;

class FatalErrorController extends LmbController
{
    function doDisplay()
    {
        re turn '123';
    }
}
