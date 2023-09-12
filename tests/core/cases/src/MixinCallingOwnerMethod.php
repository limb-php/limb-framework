<?php

namespace Tests\core\cases\src;

use limb\core\src\lmbMixin;

class MixinCallingOwnerMethod extends lmbMixin
{
    function ownerMy()
    {
        return $this->owner->my();
    }
}