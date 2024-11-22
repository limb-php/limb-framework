<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * class lmbMacroSourceLocation.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroSourceLocation
{
    public $file;
    public $line;

    function __construct($file = null, $line = null)
    {
        if ($file)
            $this->file = $file;
        else
            $this->file = 'unknown file';

        if ($line)
            $this->line = $line;
        else
            $this->line = 'unknown line';
    }

    function getFile()
    {
        return $this->file;
    }

    function getLine()
    {
        return $this->line;
    }
}

