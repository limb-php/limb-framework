<?php

namespace tests\web_app\cases\plain\src\Controllers;

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

    function doPopup($request)
    {
        return $this->closePopup();
    }

    function doWithoutPopup($request)
    {
        $this->in_popup = false;

        if ($this->in_popup)
            return $this->closePopup();
    }

    function addValidatorRule($r)
    {
        $this->validator->addRule($r);
    }

    function getErrorList()
    {
        return $this->error_list;
    }

    function set($name, $value)
    {
        $this->$name = $value;
    }
}