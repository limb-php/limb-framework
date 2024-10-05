<?php

namespace Limb\Tests\core\cases\src;

use limb\core\lmbMixin;

class MixinCallingOwnerVar extends lmbMixin
{
    function ownerVar()
    {
        return $this->owner->_get('var');
    }
}