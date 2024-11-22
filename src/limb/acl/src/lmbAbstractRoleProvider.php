<?php
/*
* Limb PHP Framework
*
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace limb\acl\src;

abstract class lmbAbstractRoleProvider
{
    protected $_role = null;

    function getRole()
    {
        if (is_null($this->_role))
            throw new lmbAclException('Role provider must have filled _role property');
        return $this->_role;
    }
}
