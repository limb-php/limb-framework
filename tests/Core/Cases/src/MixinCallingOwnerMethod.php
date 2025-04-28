<?php

namespace Limb\Tests\Core\Cases\src;

use limb\core\lmbMixin;

class MixinCallingOwnerMethod extends lmbMixin
{
    function ownerMy()
    {
        return $this->owner->my();
    }
}