<?php

namespace tests\cms\cases\Controllers;

use limb\cms\src\Controllers\lmbAdminObjectController;

class TestAdminObjectController extends lmbAdminObjectController
{
    protected $_object_class_name = AdminObjectForTesting::class;
    protected $in_popup = false;

    protected $result = '';

    function getResult($request)
    {
        return $this->result;
    }

    function doCreate($request)
    {
        parent::doCreate($request);

        return $this->result;
    }

    function doEdit($request)
    {
        parent::doEdit($request);

        return $this->result;
    }

    function doDelete($request)
    {
        parent::doDelete($request);

        return $this->result;
    }

    protected function _onBeforeSave()
    {
        $this->result .= 'onBeforeSave|';
    }

    protected function _onAfterSave()
    {
        $this->result .= 'onAfterSave|';
    }

    protected function _onBeforeValidate()
    {
        $this->result .= 'onBeforeValidate|';
    }

    protected function _onAfterValidate()
    {
        $this->result .= 'onAfterValidate|';
    }

    protected function _onBeforeImport()
    {
        $this->result .= 'onBeforeImport|';
    }

    protected function _onAfterImport()
    {
        $this->result .= 'onAfterImport|';
    }

    protected function _onBeforeCreate()
    {
        $this->result .= 'onBeforeCreate|';
    }

    protected function _onAfterCreate()
    {
        $this->result .= 'onAfterCreate|';
    }

    protected function _onCreate()
    {
        $this->result .= 'onCreate|';
    }

    protected function _onBeforeUpdate()
    {
        $this->result .= 'onBeforeUpdate|';
    }

    protected function _onUpdate()
    {
        $this->result .= 'onUpdate|';
    }

    protected function _onAfterUpdate()
    {
        $this->result .= 'onAfterUpdate|';
    }

    protected function _onBeforeDelete()
    {
        $this->result .= 'onBeforeDelete|';
    }

    protected function _onAfterDelete()
    {
        $this->result .= 'onAfterDelete|';
    }

    protected function _initCreateForm()
    {
        $this->result .= 'initCreateForm|';
    }

    protected function _initEditForm()
    {
        $this->result .= 'initEditForm|';
    }
}
