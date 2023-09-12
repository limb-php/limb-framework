<?php

namespace Tests\active_record\cases\src;

class LazyMemberForTest extends MemberForTest
{
    protected $_db_table_name = 'member_for_test';

    protected $_lazy_attributes = array('name');
}
