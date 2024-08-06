<?php

namespace tests\active_record\cases\src;

class LazyMemberForTestObject extends MemberForTestObject
{
    protected $_db_table_name = 'member_for_test';

    protected $_lazy_attributes = array('name');
}
