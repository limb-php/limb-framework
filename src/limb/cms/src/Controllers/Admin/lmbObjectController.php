<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\Controllers\Admin;

use limb\web_app\src\Controllers\LmbController;
use limb\active_record\lmbActiveRecord;
use limb\core\exception\lmbException;

/**
 * abstract class AdminObjectController.
 *
 * @package cms
 * @version $Id$
 *
 * @deprecated
 */
abstract class lmbObjectController extends LmbController
{
    protected $_object_class_name = '';

    public $item;
    public $items;

    function __construct()
    {
        parent::__construct();

        if (!$this->_object_class_name)
            throw new lmbException('Object class name is not specified');
    }

    /**
     * @return lmbActiveRecord|false
     */
    protected function _getObjectByRequestedId($request, $throw_exception = false)
    {
        if (!$id = (int)$request->get('id'))
            return false;

        if (!$item = lmbActiveRecord::findById($this->_object_class_name, $id, $throw_exception))
            return false;

        return $item;
    }

    function doDisplay($request)
    {
        $this->items = lmbActiveRecord::find($this->_object_class_name);
    }

    function doItem($request)
    {
        if (!$this->item = $this->_getObjectByRequestedId($request))
            return $this->forwardTo404();
    }
}
