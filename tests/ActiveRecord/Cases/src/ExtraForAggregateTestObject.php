<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\core\lmbObject;

class ExtraForAggregateTestObject extends lmbObject
{
    protected $extra;

    function getValue()
    {
        return $this->extra . '_as_extra_value';
    }
}
