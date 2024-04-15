<?php

namespace tests\web_app\cases\plain\src\Controllers;

use limb\core\src\exception\lmbException;
use limb\web_app\src\Controllers\LmbController;

class TestingController extends LmbController
{
    protected $name = 'foo';
    public $display_performed = false;
    public $template_name;

    function doDisplay()
    {
        $this->display_performed = true;
        $this->template_name = $this->getView()->getTemplate();

        return response();
    }

    function doWrite($request)
    {
        return "Hi!";
    }

    function doSetVars($request)
    {
        $this->item = 'item';
    }

    function doPopup()
    {
        return $this->closePopup();
    }

    function doWithoutPopup()
    {
        $this->in_popup = false;

        if ($this->in_popup)
            return $this->closePopup();
    }

    function doException()
    {
        throw new lmbException('Test');
    }

    function addValidatorRule($r)
    {
        $this->getValidator()->addRule($r);
    }

    function set($name, $value)
    {
        $this->$name = $value;
    }
}