<?php

namespace tests\cms\cases\Controllers;

use limb\cms\src\Controllers\Admin\lmbObjectController;

class TestObjectController extends lmbObjectController
{
    protected $_object_class_name = ObjectForTesting::class;
    protected $in_popup = false;
}
