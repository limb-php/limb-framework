<?php

namespace Limb\Tests\Core\Cases\src;

use limb\core\lmbMixable;

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