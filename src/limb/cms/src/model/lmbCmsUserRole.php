<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\model;

use limb\core\src\lmbObject;

/**
 * class lmbCmsUserRole.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsUserRole extends lmbObject
{
    protected $id;
    protected $name;
    protected $short_name;

    static function create($id, $name, $short_name)
    {
        $role = new static();
        $role->id = $id;
        $role->name = $name;
        $role->short_name = $short_name;

        return $role;
    }
}
