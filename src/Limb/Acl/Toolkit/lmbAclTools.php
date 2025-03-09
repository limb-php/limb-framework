<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Acl\Toolkit;

use limb\Toolkit\lmbAbstractTools;
use Limb\Acl\lmbAcl;

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
