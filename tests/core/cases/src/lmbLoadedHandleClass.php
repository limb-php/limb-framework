<?php

namespace Limb\Tests\core\cases\src;

class lmbLoadedHandleClass
{
    public $test_var;

    function __construct($value = 'default')
    {
        $this->test_var = $value;
    }

    function bar()
    {
        return 'bar';
    }
}
