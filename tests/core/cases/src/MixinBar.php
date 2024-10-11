<?php

namespace Limb\Tests\Core\Cases\src;

use limb\core\lmbMixin;

class MixinBar extends lmbMixin
{
    function bar()
    {
        return 'bar';
    }
}