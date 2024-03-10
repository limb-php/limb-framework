<?php

namespace tests\active_record\cases\src;

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
