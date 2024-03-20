<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\Controllers;

use \limb\web_app\src\Controllers\LmbController;
use limb\core\src\exception\lmbException;
use limb\active_record\src\lmbActiveRecord;

/**
 * abstract class AdminObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class AdminObjectController extends LmbController
{
    protected $_form_name = 'object_form';
    protected $_object_class_name = '';
    protected $_popup = true;
    protected $_back_url = array();

    protected $item = null;

    function __construct()
    {
        parent::__construct();

        if (!$this->_object_class_name)
            throw new lmbException('Object class name is not specified');
    }

    protected function _passLocalAttributesToView()
    {
        //passing back_url string into view
        if (is_array($this->_back_url))
            $this->back_url = $this->toolkit->getRoutesUrl($this->_back_url);
        else
            $this->back_url = $this->_back_url;

        parent::_passLocalAttributesToView();
    }

    function doCreate($request)
    {
        $this->item = new $this->_object_class_name();
        $this->_onCreate($request);

        $this->useForm($this->_form_name);
        $this->setFormDatasource($this->item);

        if ($request->hasPost()) {
            $this->_import($request);
            $this->_validateAndSave(true);
        } else {
            $this->item->import($request);
            $this->_initCreateForm($request);
        }
    }

    function doEdit($request)
    {
        $this->item = lmbActiveRecord::findById($this->_object_class_name, (int)$request->get('id'));
        $this->_onEdit($request);

        $this->useForm($this->_form_name);
        $this->setFormDatasource($this->item);

        if ($request->hasPost()) {
            $this->_import($request);
            $this->_validateAndSave(false);
        } else {
            $this->_initEditForm($request);
        }
    }

    function doDelete($request)
    {
        if ($request->hasPost())
            $this->_onBeforeDelete($request);

        if ($request->get('delete') || $request->get('do_action')) {
            foreach ($request->getArray('ids') as $id) {
                $item = new $this->_object_class_name((int)$id);
                $item->destroy();
            }
            $this->_endDialog();
            $this->_onAfterDelete($request);
        }
    }

    function performPublishCommand()
    {
        $this->performCommand(lmbCmsPublishObjectCommand::class, $this->_object_class_name);
    }

    function performUnpublishCommand()
    {
        $this->performCommand(lmbCmsUnpublishObjectCommand::class, $this->_object_class_name);
    }

    protected function _import($request)
    {
        $this->_onBeforeImport($request);
        $this->item->import($request->export());
        $this->_onAfterImport($request);
    }

    protected function _validate($request)
    {
        $this->_onBeforeValidate($request);
        $this->item->validate($this->error_list);
        $this->_onAfterValidate($request);
    }

    protected function _save($request, $is_create)
    {
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
    }

    protected function _validateAndSave($request, $is_create = false)
    {
        $this->_validate($request);

        if ($this->error_list->isValid()) {
            $this->_save($request, $is_create);

            $this->_endDialog();
        }
    }

    protected function _endDialog()
    {
        if ($this->_popup)
            $this->closePopup();
        else
            $this->redirect($this->_back_url);
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

    protected function _onAfterCreate($request)
    {
    }

    protected function _onBeforeUpdate($request)
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

    protected function _onEdit($request)
    {
    }

    protected function _onCreate($request)
    {
    }
}

