<?php
namespace Tests\web_app\cases\plain\src\Controllers;

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
        $this->closePopup();
    }

    function doWithoutPopup($request)
    {
        $this->in_popup = false;
        $this->closePopup();
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