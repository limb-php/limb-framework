<?php

namespace Tests\active_record\cases\src;

class TestingNullValueObject extends TestingValueObject
{
    function getValue()
    {
        return 'i\'m a null';
    }
}
