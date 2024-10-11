<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class TestingNullValueObject extends TestingValueObject
{
    function getValue()
    {
        return 'i\'m a null';
    }
}
