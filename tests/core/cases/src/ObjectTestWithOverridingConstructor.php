<?php

namespace Limb\Tests\Core\Cases\src;

use limb\core\lmbObject;

class ObjectTestWithOverridingConstructor extends lmbObject
{
    protected $pro = true;
    public $_guarded = false;

    function __construct()
    {
    }
}