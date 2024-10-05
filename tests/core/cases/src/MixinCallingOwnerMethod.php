<?php

namespace Limb\Tests\core\cases\src;

use limb\core\lmbMixin;

class MixinCallingOwnerMethod extends lmbMixin
{
    function ownerMy()
    {
        return $this->owner->my();
    }
}