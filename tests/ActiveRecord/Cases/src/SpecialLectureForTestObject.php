<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

class SpecialLectureForTestObject extends LectureForTestObject
{
    protected $_default_sort_params = array('id' => 'DESC');
}
