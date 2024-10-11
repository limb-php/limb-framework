<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class TestingValueObject
{
    var $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    function getValue()
    {
        return $this->value;
    }
}
