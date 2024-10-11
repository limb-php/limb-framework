<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

// Aggregate must implement lmbSet interface.
use limb\core\lmbObject;

class NameForAggregateTest extends lmbObject
{
    protected $first;
    protected $last;

    function getFull()
    {
        return $this->first . ' ' . $this->last;
    }
}
