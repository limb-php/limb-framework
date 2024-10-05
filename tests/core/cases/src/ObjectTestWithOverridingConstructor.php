<?php

namespace Limb\Tests\core\cases\src;

use limb\core\lmbObject;

class ObjectTestWithOverridingConstructor extends lmbObject
{
    protected $pro = true;
    public $_guarded = false;

    function __construct()
    {
    }
}