<?php

namespace Limb\Tests\core\cases\src;

use limb\core\lmbObject;

class ObjectTestVersion extends lmbObject
{
    public $bar;
    protected $protected = 'me';
    public $_guarded = '';

    function getBar()
    {
        return $this->bar . '_get_called';
    }

    function setBar($value)
    {
        $this->bar = $value . '_set_called';
    }

    function isOk()
    {
        return true;
    }

    function getIsError()
    {
        return true;
    }

    function isError()
    {
        return false;
    }
}