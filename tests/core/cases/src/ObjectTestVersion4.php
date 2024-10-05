<?php

namespace Limb\Tests\core\cases\src;

use limb\core\lmbObject;

class ObjectTestVersion4 extends lmbObject
{
    function rawGet($name)
    {
        return $this->_getRaw($name);
    }
}