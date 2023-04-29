<?php

namespace tests\cms\cases\Controllers;

use limb\cms\src\Controllers\lmbAdminObjectController;

class TestAdminObjectController extends lmbAdminObjectController
{
    protected $_object_class_name = AdminObjectForTesting::class;
    protected $in_popup = false;

    protected function _onBeforeSave() { response()->append('onBeforeSave|'); }
    protected function _onAfterSave() { response()->append('onAfterSave|'); }

    protected function _onBeforeValidate() { response()->append('onBeforeValidate|'); }
    protected function _onAfterValidate() { response()->append('onAfterValidate|'); }

    protected function _onBeforeImport() { response()->append('onBeforeImport|'); }
    protected function _onAfterImport() { response()->append('onAfterImport|'); }

    protected function _onBeforeCreate() { response()->append('onBeforeCreate|'); }
    protected function _onAfterCreate() { response()->append('onAfterCreate|'); }
    protected function _onCreate() { response()->append('onCreate|'); }

    protected function _onBeforeUpdate() { response()->append('onBeforeUpdate|'); }
    protected function _onUpdate() { response()->append('onUpdate|'); }
    protected function _onAfterUpdate() { response()->append('onAfterUpdate|'); }

    protected function _onBeforeDelete() { response()->append('onBeforeDelete|'); }
    protected function _onAfterDelete() { response()->append('onAfterDelete|'); }

    protected function _initCreateForm() { response()->append('initCreateForm|'); }
    protected function _initEditForm() { response()->append('initEditForm|'); }
}
