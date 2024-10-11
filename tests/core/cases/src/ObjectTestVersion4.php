<?php

namespace Limb\Tests\Core\Cases\src;

use limb\core\lmbObject;

class ObjectTestVersion4 extends lmbObject
{
    function rawGet($name)
    {
        return $this->_getRaw($name);
    }
}