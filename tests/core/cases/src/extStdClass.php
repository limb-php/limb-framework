<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases\src;

class extStdClass extends \stdClass
{
    function __toString()
    {
        return '';
    }
}