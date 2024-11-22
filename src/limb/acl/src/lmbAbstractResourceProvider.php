<?php
/*
* Limb PHP Framework
*
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace limb\acl\src;

abstract class lmbAbstractResourceProvider
{
    protected $_resource = null;

    function getResource()
    {
        if (is_null($this->_resource))
            throw new lmbAclException('Resource provider must have filled _resource property');
        return $this->_resource;
    }
}
