<?php

namespace tests\active_record\cases\src;

use limb\core\src\lmbObject;

class ExtraForAggregateTestObject extends lmbObject
{
    protected $extra;

    function getValue()
    {
        return $this->extra . '_as_extra_value';
    }
}
