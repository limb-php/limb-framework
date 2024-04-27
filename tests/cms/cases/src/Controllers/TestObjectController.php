<?php

namespace tests\cms\cases\src\Controllers;

use limb\cms\src\Controllers\Admin\lmbObjectController;
use tests\cms\cases\src\Model\ObjectForTesting;

class TestObjectController extends lmbObjectController
{
    protected $_object_class_name = ObjectForTesting::class;
    protected $in_popup = false;

    public function doTestExtraParams($id)
    {
        return $id;
    }
}
