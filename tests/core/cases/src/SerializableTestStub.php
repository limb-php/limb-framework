<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases\src;

class SerializableTestStub
{
    protected $child;

    function __construct()
    {
        $this->child = new SerializableTestChildStub();
    }

    function identify()
    {
        return 'parent';
    }

    function getChild()
    {
        return $this->child;
    }
}
