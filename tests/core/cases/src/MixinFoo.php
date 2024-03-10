<?php

namespace tests\core\cases\src;

use limb\core\src\lmbMixin;

class MixinFoo extends lmbMixin
{
    function foo()
    {
        return 'foo';
    }
}