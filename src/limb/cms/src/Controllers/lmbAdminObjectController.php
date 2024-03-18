<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\Controllers;

use limb\dbal\src\lmbDBAL;
use limb\active_record\src\lmbActiveRecord;
use limb\toolkit\src\lmbToolkit;

/**
 * abstract class AdminObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class lmbAdminObjectController extends lmbObjectController
{
    protected $_form_name = 'object_form';
    protected $_popup = true;
    protected $_back_url = array();

    protected function _passLocalAttributesToView()
    {
        //passing back_url string into view
        if (is_array($this->_back_url))
            $this->back_url = $this->toolkit->getRoutesUrl($this->_back_url);
        else
            $this->back_url = $this->_back_url;

        parent::_passLocalAttributesToView();
    }


    function doDisplay($request)
    {
        $this->items = lmbActiveRecord::find($this->_object_class_name);
        $this->_applySortParams($request);
    }

    protected function _applySortParams($request)
    {
        $sort = $request->getGetFiltered('sort', FILTER_SANITIZE_SPECIAL_CHARS, false);

        $direction = $request->getGet('direction');
        if (!in_array($direction, array('asc', 'desc')))
            $direction = 'asc';

        if ($sort == false) return;
        $this->items->sort(array($sort => $direction));
    }

    function doCreate($request)
    {
        $this->item = new $this->_object_class_name();
        $this->_onCreate($request);

        $this->useForm($this->_form_name);
        $this->setFormDatasource($this->item);

        if ($request->hasPost()) {
            $this->_import($request);
            $this->_validateAndSave($request, true);
        } else {
            $this->_initCreateForm($request);
        }
    }

    function doEdit($request)
    {
        if (!$this->item = $this->_getObjectByRequestedId($request))
            return $this->forwardTo404();
        $this->_onUpdate($request);
        $this->useForm($this->_form_name);
        $this->setFormDatasource($this->item);

        if ($request->hasPost()) {
            $this->_import($request);
            $this->_validateAndSave($request, false);
        } else {
            $this->_initEditForm($request);
        }
    }

    function doDelete($request)
    {
        if (!$request->hasPost())
            $ids = $request->get('ids');
        else
            $ids = $request->getPost('ids');

        if (!is_array($ids))
            $ids = array($ids);

        $this->items = lmbActiveRecord::findByIds($this->_object_class_name, $ids);

        if (!$request->hasPost())
            return;

        $this->_onBeforeDelete($request);

        foreach ($this->items as $item)
            $item->destroy();

        $this->_onAfterDelete($request);

        return $this->_endDialog();
    }

    function doRevertPublish($request)
    {
        if ($request->has('ids'))
            $ids = $request->get('ids');
        elseif ($request->has('id'))
            $ids = array($request->get('id'));
        else
            return;

        $info_object = new $this->_object_class_name();

        foreach ($ids as $id)
            lmbDBAL::execute('UPDATE ' . $info_object->getTableName() . ' SET is_published = IF(is_published > 0, 0, 1) WHERE id = ' . lmbToolkit::instance()->getDefaultDbConnection()->escape($id));

        return $this->_endDialog();
    }

    function doPublish($request)
    {
        if (!$item = $this->_getObjectByRequestedId($request))
            return $this->forwardTo404();

        $this->_onBeforePublish($request);
        $item->setIsPublished(1);
        $item->save();
        $this->_onAfterPublish($request);

        return $this->_endDialog();
    }

    function doUnpublish($request)
    {
        if (!$item = $this->_getObjectByRequestedId($request))
            return $this->forwardTo404();

        $this->_onBeforeUnpublish($request);
        $item->setIsPublished(0);
        $item->save();
        $this->_onAfterUnpublish($request);

        return $this->_endDialog();
    }

    function doPriority($request)
    {
        $this->_changeItemsPriority($this->_object_class_name);
        return $this->_endDialog();
    }

    protected function _import($request)
    {
        $this->_onBeforeImport($request);
        $this->item->import($request);
        if ($request->hasFiles()) {
            foreach ($request->getFiles() as $field => $file)
                $this->item->set($field, $file->getName());
        }
        $this->_onAfterImport($request);
    }

    protected function _validateAndSave($request, $is_create = false)
    {
        $this->_onBeforeValidate($request);
        $this->item->validate($this->error_list);
        $this->_onAfterValidate($request);

        if ($this->error_list->isValid()) {
            if ($is_create)
                $this->_onBeforeCreate($request);
            else
                $this->_onBeforeUpdate($request);

            $this->_onBeforeSave($request);
            $this->item->saveSkipValidation();
            $this->_onAfterSave($request);

            if ($is_create)
                $this->_onAfterCreate($request);
            else
                $this->_onAfterUpdate($request);

            return $this->_endDialog();
        }
    }

    protected function _endDialog()
    {
        if ($this->_popup) {
            return $this->closePopup();
        }
        else {
            return $this->redirect($this->_back_url);
        }
    }

    protected function _changeItemsPriority($model, $where_field, $where_field_value)
    {
        $priority_items = $request->get('priority_items');

        $info_item = new $model();
        $sql = 'SELECT id, priority FROM ' . $info_item->getTableName() . ' WHERE ' . $where_field . '=' . $where_field_value;
        $current_priorities_object = lmbDBAL::fetch($sql);
        $current_priorities_object = $current_priorities_object->getArray();

        $current_priorities = array();
        foreach ($current_priorities_object as $item)
            $current_priorities[$item->get('id')] = $item->get('priority');

        foreach ($priority_items as $id => $priority)
            $current_priorities[$id] = $priority;

        asort($current_priorities);

        $i = 10;

        $table_name = $info_item->getTableName();

        foreach ($current_priorities as $id => $priority) {
            $sql = "UPDATE " . $table_name . " SET priority='" . $i . "' WHERE id='" . $id . "'";
            lmbDBAL::execute($sql);
            $i += 10;
        }

    }

    protected function _initCreateForm($request)
    {
    }

    protected function _initEditForm($request)
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

    protected function _onCreate($request)
    {
    }

    protected function _onAfterCreate($request)
    {
    }

    protected function _onBeforeUpdate($request)
    {
    }

    protected function _onUpdate($request)
    {
    }

    protected function _onAfterUpdate($request)
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

    protected function _onBeforeImport($request)
    {
    }

    protected function _onAfterImport($request)
    {
    }

    protected function _onBeforePublish($request)
    {
    }

    protected function _onAfterPublish($request)
    {
    }

    protected function _onBeforeUnpublish($request)
    {
    }

    protected function _onAfterUnpublish($request)
    {
    }
}
