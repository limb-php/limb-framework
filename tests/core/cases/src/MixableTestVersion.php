<?php

namespace tests\core\cases\src;

use limb\core\src\lmbMixable;

class MixableTestVersion extends lmbMixable
{
    protected $var = 'var';

    function __construct($mixins = array())
    {
        $this->mixins = $mixins;
    }

    function my()
    {
        return 'my';
    }
}