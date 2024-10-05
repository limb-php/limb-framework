<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace Limb\Tests\core\cases\src;

class extStdClass extends \stdClass
{
    function __toString()
    {
        return '';
    }
}