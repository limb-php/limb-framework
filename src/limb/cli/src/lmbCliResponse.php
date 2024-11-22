<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbCliResponse.
 *
 * @package cli
 * @version $Id: lmbCliResponse.php 7686 2009-03-04 19:57:12Z
 */

namespace limb\cli\src;

class lmbCliResponse
{
    protected $verbose = true;

    function __construct($verbose = true)
    {
        $this->verbose = $verbose;
    }

    function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    function write($msg)
    {
        if ($this->verbose)
            echo $msg;
    }
}
