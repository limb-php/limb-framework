<?php

namespace tests\core\cases\src;

use limb\core\src\lmbMixin;

class MixinOverridinFoo extends lmbMixin
{
    function foo()
    {
        return 'overriden foo';
    }
}