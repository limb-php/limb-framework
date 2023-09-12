<?php

namespace Tests\core\cases\src;

use limb\core\src\lmbObject;

class ObjectTestVersion3 extends lmbObject
{
    protected $protected;
    protected $protected2;

    public $getter_called_count = 0;
    public $setter_called_count = 0;

    function setProtected($value)
    {
        $this->setter_called_count++;
        $this->protected = $value;
    }

    function getProtected()
    {
        $this->getter_called_count++;
        return $this->protected;
    }

    function rawSet($value)
    {
        $this->_setRaw('protected', $value);
    }

    function rawGet()
    {
        return $this->_getRaw('protected');
    }
}