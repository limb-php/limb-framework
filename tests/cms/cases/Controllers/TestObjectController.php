<?php

namespace Tests\cms\cases\Controllers;

use limb\cms\src\Controllers\lmbObjectController;

class TestObjectController extends lmbObjectController
{
    protected $_object_class_name = ObjectForTesting::class;
    protected $in_popup = false;
}
