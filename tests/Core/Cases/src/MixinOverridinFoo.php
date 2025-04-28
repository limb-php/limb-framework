<?php

namespace Limb\Tests\Core\Cases\src;

use limb\core\lmbMixin;

class MixinOverridinFoo extends lmbMixin
{
    function foo()
    {
        return 'overriden foo';
    }
}