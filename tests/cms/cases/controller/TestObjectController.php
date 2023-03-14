<?php

namespace tests\cms\cases\controller;

use limb\cms\src\controller\lmbObjectController;

class TestObjectController extends lmbObjectController
{
    protected $_object_class_name = ObjectForTesting::class;
    protected $in_popup = false;
}
