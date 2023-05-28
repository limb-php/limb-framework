<?php

namespace tests\core\cases\src;

class lmbHandleDeclaredInSameFile
{
    var $test_var;
    var $test_var2;

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