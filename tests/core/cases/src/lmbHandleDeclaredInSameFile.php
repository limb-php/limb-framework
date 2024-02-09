<?php

namespace Tests\core\cases\src;

class lmbHandleDeclaredInSameFile
{
    public $test_var;
    public $test_var2;

    function __construct($var = 'default', $var2 = 'default')
    {
        $this->test_var = $var;
        $this->test_var2 = $var2;
    }

    function foo()
    {
        return 'foo';
    }
}
