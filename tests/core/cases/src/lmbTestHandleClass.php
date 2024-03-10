<?php

namespace tests\core\cases\src;

class lmbTestHandleClass
{
    public $test_var;

    function __construct($var = 'default')
    {
        $this->test_var = $var;
    }
}
