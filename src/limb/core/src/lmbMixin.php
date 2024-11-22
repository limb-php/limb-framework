<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * class lmbMixin.
 *
 * @package core
 * @version $Id$
 */
class lmbMixin
{
    protected $owner;

    function setOwner($owner)
    {
        $this->owner = $owner;
    }

}

