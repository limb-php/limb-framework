<?php

namespace tests\cms\cases\Controllers;

use limb\cms\src\Controllers\Admin\lmbAdminObjectController;
use limb\net\src\lmbHttpResponse;

class TestAdminObjectController extends lmbAdminObjectController
{
    protected $_object_class_name = AdminObjectForTesting::class;
    protected $in_popup = false;

    protected $result = '';

    function getResult()
    {
        return $this->result;
    }

    function doCreate($request)
    {
        parent::doCreate($request);

        return new lmbHttpResponse($this->result);
    }

    function doEdit($request)
    {
        parent::doEdit($request);

        return new lmbHttpResponse($this->result);
    }

    function doDelete($request)
    {
        parent::doDelete($request);

        return new lmbHttpResponse($this->result);
    }

    protected function _onBeforeSave($request)
    {
        $this->result .= 'onBeforeSave|';
    }

    protected function _onAfterSave($request)
    {
        $this->result .= 'onAfterSave|';
    }

    protected function _onBeforeValidate($request)
    {
        $this->result .= 'onBeforeValidate|';
    }

    protected function _onAfterValidate($request)
    {
        $this->result .= 'onAfterValidate|';
    }

    protected function _onBeforeImport($request)
    {
        $this->result .= 'onBeforeImport|';
    }

    protected function _onAfterImport($request)
    {
        $this->result .= 'onAfterImport|';
    }

    protected function _onBeforeCreate($request)
    {
        $this->result .= 'onBeforeCreate|';
    }

    protected function _onAfterCreate($request)
    {
        $this->result .= 'onAfterCreate|';
    }

    protected function _onCreate($request)
    {
        $this->result .= 'onCreate|';
    }

    protected function _onBeforeUpdate($request)
    {
        $this->result .= 'onBeforeUpdate|';
    }

    protected function _onUpdate($request)
    {
        $this->result .= 'onUpdate|';
    }

    protected function _onAfterUpdate($request)
    {
        $this->result .= 'onAfterUpdate|';
    }

    protected function _onBeforeDelete($request)
    {
        $this->result .= 'onBeforeDelete|';
    }

    protected function _onAfterDelete($request)
    {
        $this->result .= 'onAfterDelete|';
    }

    protected function _initCreateForm($request)
    {
        $this->result .= 'initCreateForm|';
    }

    protected function _initEditForm($request)
    {
        $this->result .= 'initEditForm|';
    }
}
