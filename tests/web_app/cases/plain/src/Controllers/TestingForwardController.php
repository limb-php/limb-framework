<?php

namespace tests\web_app\cases\plain\src\Controllers;

use limb\web_app\src\Controllers\LmbController;

class TestingForwardController extends LmbController
{
    function doForward()
    {
        return $this->forward(TestingController::class, 'write');
    }
}
