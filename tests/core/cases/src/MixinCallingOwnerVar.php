<?php

namespace Tests\core\cases\src;

use limb\core\src\lmbMixin;

class MixinCallingOwnerVar extends lmbMixin
{
    function ownerVar()
    {
        return $this->owner->_get('var');
    }
}