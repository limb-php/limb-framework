<?php

namespace tests\core\cases\src;

use limb\core\src\lmbMixin;

class MixinBar extends lmbMixin
{
    function bar()
    {
        return 'bar';
    }
}