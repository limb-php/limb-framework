<?php

namespace Limb\Tests\core\cases\src;

use limb\core\lmbMixin;

class MixinOverridinFoo extends lmbMixin
{
    function foo()
    {
        return 'overriden foo';
    }
}