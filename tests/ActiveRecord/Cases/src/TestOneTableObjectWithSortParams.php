<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class TestOneTableObjectWithSortParams extends TestOneTableObject
{
    protected $_default_sort_params = array('id' => 'DESC');
}
