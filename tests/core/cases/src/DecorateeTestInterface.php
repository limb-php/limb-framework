<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases\src;

interface DecorateeTestInterface
{
    function set($value);

    function get();

    function typehint(DecorateeTestStub $value);
}
