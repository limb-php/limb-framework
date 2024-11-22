<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases\src;

class DecorateeTestStub implements DecorateeTestInterface
{
    protected $value;

    function set($value)
    {
        $this->value = $value;
    }

    function get()
    {
        return $this->value;
    }

    function typehint(DecorateeTestStub $value)
    {
    }
}
