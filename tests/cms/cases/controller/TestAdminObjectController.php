<?php

namespace tests\cms\cases\controller;

use limb\cms\src\controller\lmbAdminObjectController;

class TestAdminObjectController extends lmbAdminObjectController
{
    protected $_object_class_name = AdminObjectForTesting::class;
    protected $in_popup = false;

    protected function _onBeforeSave() { $this->response->append('onBeforeSave|'); }
    protected function _onAfterSave() { $this->response->append('onAfterSave|'); }

    protected function _onBeforeValidate() { $this->response->append('onBeforeValidate|'); }
    protected function _onAfterValidate() { $this->response->append('onAfterValidate|'); }

    protected function _onBeforeImport() { $this->response->append('onBeforeImport|'); }
    protected function _onAfterImport() { $this->response->append('onAfterImport|'); }

    protected function _onBeforeCreate() { $this->response->append('onBeforeCreate|'); }
    protected function _onAfterCreate() { $this->response->append('onAfterCreate|'); }
    protected function _onCreate() { $this->response->append('onCreate|'); }

    protected function _onBeforeUpdate() { $this->response->append('onBeforeUpdate|'); }
    protected function _onUpdate() { $this->response->append('onUpdate|'); }
    protected function _onAfterUpdate() { $this->response->append('onAfterUpdate|'); }

    protected function _onBeforeDelete() { $this->response->append('onBeforeDelete|'); }
    protected function _onAfterDelete() { $this->response->append('onAfterDelete|'); }

    protected function _initCreateForm() { $this->response->append('initCreateForm|'); }
    protected function _initEditForm() { $this->response->append('initEditForm|'); }
}
