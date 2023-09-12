<?php

namespace Tests\core\cases\src;

use limb\core\src\lmbObject;

class ObjectTestVersion4 extends lmbObject
{
    function rawGet($name)
    {
        return $this->_getRaw($name);
    }
}