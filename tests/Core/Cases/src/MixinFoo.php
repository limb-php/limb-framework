<?php

namespace Limb\Tests\Core\Cases\src;

use limb\core\lmbMixin;

class MixinFoo extends lmbMixin
{
    function foo()
    {
        return 'foo';
    }
}