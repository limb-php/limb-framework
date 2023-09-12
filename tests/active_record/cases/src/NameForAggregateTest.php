<?php

namespace Tests\active_record\cases\src;

// Aggregate must implement lmbSet interface.
use limb\core\src\lmbObject;

class NameForAggregateTest extends lmbObject
{
    protected $first;
    protected $last;

    function getFull()
    {
        return $this->first . ' ' . $this->last;
    }
}
