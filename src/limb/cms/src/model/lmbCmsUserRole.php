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

    function __construct($id, $name, $short_name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->short_name = $short_name;
    }
}
