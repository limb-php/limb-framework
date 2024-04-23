<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\Controllers\Admin;

use \limb\web_app\src\Controllers\LmbController;
use limb\cms\src\model\lmbCmsNode;
use limb\core\src\exception\lmbException;
use limb\active_record\src\lmbActiveRecord;

/**
 * abstract class NodeWithObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class NodeWithObjectController extends LmbController
{
    protected $_form_name = 'object_form';
    protected $_controller_name = '';
    protected $_node_class_name = lmbCmsNode::class;
    protected $_object_class_name = '';
    protected $_generate_identifier = false;

    protected $node = null;
    protected $item = null;

    function __construct()
    {
        parent::__construct();

        if (!$this->_object_class_name || !$this->_controller_name)
            throw new lmbException('Object class name or(and) controller name is not specified');
    }

    function doCreate($request)
    {
        $this->node = new $this->_node_class_name();
        $this->item = new $this->_object_class_name();

        $this->useForm($this->_form_name);
        $this->setFormDatasource($request);

        if ($request->hasPost()) {
            $this->node->setControllerName($this->_controller_name);
            $this->node->setObject($this->item);
            $this->item->setNode($this->node);
            $this->_import($request);

            if ($this->_generate_identifier || $request->get('auto_identifier'))
                $this->node->setIdentifier(lmbCmsNode::generateIdentifier($request->get('parent')));

            $this->_validateAndSave($request, true);
        } else {
            $this->_initCreateForm($request);
        }
    }

    function doEdit($request)
    {
        $this->node = lmbActiveRecord::findById($this->_node_class_name, $request->get('id'), false);
        if (!$this->node)
            return $this->forwardTo404();

        $this->item = $this->node->getObject();
        $this->useForm($this->_form_name);
        $this->setFormDatasource($request);

        if ($request->hasPost()) {
            $this->_import($request);
            $this->_validateAndSave(false);
        } else {
            $this->_initEditForm($request);
        }
    }

    protected function _import($request)
    {
        $this->node->import($request->export());
        $this->item->import($request->export());
    }

    protected function _validateAndSave($request, $is_create = false)
    {
        $this->_onBeforeValidate($request);
        $this->node->validate($this->error_list);
        $this->item->validate($this->error_list);
        $this->_onAfterValidate($request);

        if ($this->error_list->isValid()) {
            if ($is_create)
                $this->_onBeforeCreate($request);
            else
                $this->_onBeforeEdit($request);

            $this->_onBeforeSave($request);
            $this->node->saveSkipValidation();
            //$this->item->saveSkipValidation($request);
            $this->_onAfterSave($request);

            if ($is_create)
                $this->_onAfterCreate($request);
            else
                $this->_onAfterEdit($request);

            $this->closePopup();
        }
    }

    protected function _initEditForm($request)
    {
        $request->merge($this->node->export());
        $request->merge($this->item->export());
        $request->set('node', $this->node);
        $request->set('item', $this->item);
    }

    function performPublishCommand($request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || !count($ids))
            $this->closePopup();

        foreach ($ids as $id) {
            $item = new $this->_object_class_name((int)$id);
            $item->setIsPublished(true);
            $item->save();
        }

        $this->closePopup();
    }

    function performUnpublishCommand($request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || !count($ids))
            $this->closePopup();

        foreach ($ids as $id) {
            $item = new $this->_object_class_name((int)$id);
            $item->setIsPublished(false);
            $item->save();
        }

        $this->closePopup();
    }

    function doDelete($request)
    {
        if ($request->hasPost())
            $this->_onBeforeDelete($request);

        if ($request->get('delete')) {
            foreach ($request->get('ids') as $id) {
                $node = lmbActiveRecord::findById(lmbCmsNode::class, (int)$id);
                $node->destroy();
            }

            $this->_onAfterDelete($request);
            $this->closePopup();
        }
    }

    protected function _initCreateForm($request)
    {
    }

    protected function _onBeforeSave($request)
    {
    }

    protected function _onAfterSave($request)
    {
    }

    protected function _onBeforeCreate($request)
    {
    }

    protected function _onAfterCreate($request)
    {
    }

    protected function _onBeforeEdit($request)
    {
    }

    protected function _onAfterEdit($request)
    {
    }

    protected function _onBeforeDelete($request)
    {
    }

    protected function _onAfterDelete($request)
    {
    }

    protected function _onBeforeValidate($request)
    {
    }

    protected function _onAfterValidate($request)
    {
    }
}
