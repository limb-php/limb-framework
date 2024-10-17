<?php

namespace limb\optimization\src\controller;

use limb\cms\src\Controllers\Admin\AdminObjectController;
use limb\optimization\src\model\MetaInfo;

class AdminSeoController extends AdminObjectController
{
    protected $_object_class_name = MetaInfo::class;

    /* */
    public function doDisplay($request)
    {
        $this->items = MetaInfo::findForAdmin(array('sort' => array('url' => 'ASC')));
    }

    public function doCreateEditByUrl($request)
    {
        $this->item = MetaInfo::findByUrl($url = $request->get('url'));
        if (!$this->item) {
            $this->setTemplate('admin_seo/create.phtml');

            $this->item = new MetaInfo();
            $this->item->setUrl($url);

            $this->_onCreate($request);

            $this->useForm($this->_form_name);
            $this->setFormDatasource($this->item);

            if ($request->hasPost()) {
                $this->_import($request);
                $this->_validate($request);
                $this->_store($request);
            } else {
                $this->item->import($request);
                $this->_initCreateForm($request);
            }
        } else {
            $this->setTemplate('admin_seo/edit.phtml');

            $this->_onEdit($request);

            $this->useForm($this->_form_name);
            $this->setFormDatasource($this->item);

            if ($request->hasPost()) {
                $this->_import($request);
                $this->_validate($request);
                $this->_update($request);
            } else {
                $this->_initEditForm($request);
            }
        }
    }
}
