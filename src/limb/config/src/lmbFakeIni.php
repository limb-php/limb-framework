<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\config\src;

use limb\config\src\lmbIni;

/**
 * class lmbFakeIni.
 *
 * @package config
 * @version $Id$
 */
class lmbFakeIni extends lmbIni
{
    function __construct($contents)
    {
        $this->_parseLines(explode("\n", $contents));
    }
}


