<?php

namespace Tests\active_record\cases\src;

class SpecialLectureForTestObject extends LectureForTestObject
{
    protected $_default_sort_params = array('id' => 'DESC');
}
