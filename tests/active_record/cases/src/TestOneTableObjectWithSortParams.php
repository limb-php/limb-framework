<?php

namespace tests\active_record\cases\src;

class TestOneTableObjectWithSortParams extends TestOneTableObject
{
    protected $_default_sort_params = array('id' => 'DESC');
}
