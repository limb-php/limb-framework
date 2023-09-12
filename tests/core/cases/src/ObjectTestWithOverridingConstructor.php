<?php

namespace Tests\core\cases\src;

use limb\core\src\lmbObject;

class ObjectTestWithOverridingConstructor extends lmbObject
{
    protected $pro = true;
    public $_guarded = false;

    function __construct()
    {
    }
}