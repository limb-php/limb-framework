<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\acl\src\toolkit;

use limb\toolkit\src\lmbAbstractTools;
use limb\acl\src\lmbAcl;

/**
 * class lmbFsTools.
 *
 * @package fs
 * @version $Id$
 */
class lmbAclTools extends lmbAbstractTools
{
    protected $acl = null;

    function getAcl(): lmbAcl
    {
        if (is_null($this->acl))
            $this->acl = new lmbAcl();

        return $this->acl;
    }

    function setAcl($acl): void
    {
        $this->acl = $acl;
    }
}
