<?php

namespace tests\web_app\cases\plain\src\Controllers;

use limb\core\src\exception\lmbException;
use limb\web_app\src\Controllers\LmbController;

class TestingController extends LmbController
{
    protected $name = 'foo';
    public $display_performed = false;
    public $template_name;
    public $item;
    public $in_popup;
    public $params = [];
    public $foo;
    public $bar;

    function doDisplay()
    {
        $this->display_performed = true;
        //$this->template_name = $this->getView()->getTemplate();
        $this->template_name = 'some_template.html';

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

        return '';
    }

    function doException()
    {
        throw new lmbException('Test');
    }

    function doMemoryLimit()
    {
        ini_set('memory_limit', '1M');

        $x = "123456789ABCDEF";
        while (true)
            $x .= $x;
    }

    function addValidatorRule($r)
    {
        $this->getValidator()->addRule($r);
    }

    function set($name, $value)
    {
        if(property_exists($this, $name))
            $this->$name = $value;
        else
            $this->params[$name] = $value;
    }
}