<?php

namespace tests\core\cases\src;

use limb\core\src\lmbObject;

class ObjectTestVersion5 extends lmbObject
{
    function getBar()
    {
        return 'foo';
    }
}