<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class GroupForTestObjectStub extends GroupForTestObject
{
    var $save_calls = 0;

    function save($error_list = null)
    {
        parent::save($error_list);
        $this->save_calls++;
    }
}
