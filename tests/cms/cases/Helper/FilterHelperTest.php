<?php

namespace tests\cms\cases\Helper;


use limb\cms\src\Helper\lmbCmsAdminFilterHelper;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . '/../.setup.php');

class FilterHelperTest extends TestCase
{
    function testInitHelper()
    {
        $filter_name = 'test_filter';
        $request = new lmbHttpRequest('/');

        $filter_helper = new lmbCmsAdminFilterHelper($filter_name, $request);
        $filter_helper->setFilter('id', '');
        $filter_helper->setFilter('login', '');
        $filter_helper->setFilter('role_id', '');
        $filter_helper->setFilter('start_balance', 0);
        $filter_helper->setFilter('finish_balance', 1000);

        $this->assertEquals(['id' => '', 'login' => '', 'role_id' => '', 'start_balance' => 0, 'finish_balance' => 1000], $filter_helper->getParams());
    }

    function testAfterPostHelper()
    {
        $filter_name = 'test_filter';
        $request = new lmbHttpRequest('/', 'POST', [], ['id' => 1, 'finish_balance' => 2000]);

        $filter_helper = new lmbCmsAdminFilterHelper($filter_name, $request);
        $filter_helper->setFilter('id', '');
        $filter_helper->setFilter('login', '');
        $filter_helper->setFilter('role_id', '');
        $filter_helper->setFilter('start_balance', 0);
        $filter_helper->setFilter('finish_balance', 1000);

        $this->assertEquals(['id' => 1, 'login' => '', 'role_id' => '', 'start_balance' => 0, 'finish_balance' => 2000], $filter_helper->getParams());
    }

    function testAfterPostHelperWithOtherParams()
    {
        $filter_name = 'test_filter';
        $request = new lmbHttpRequest('/', 'POST', [], ['id' => 1, 'finish_balance' => 2000, 'other_param1' => 'value1']);

        $filter_helper = new lmbCmsAdminFilterHelper($filter_name, $request);
        $filter_helper->setFilter('id', '');
        $filter_helper->setFilter('login', '');
        $filter_helper->setFilter('role_id', '');
        $filter_helper->setFilter('start_balance', 0);
        $filter_helper->setFilter('finish_balance', 1000);

        $this->assertEquals(['id' => 1, 'login' => '', 'role_id' => '', 'start_balance' => 0, 'finish_balance' => 2000], $filter_helper->getParams());
    }

    function testAfterPostHelperWithOtherParamsNoRequest()
    {
        $toolkit = lmbToolkit::instance();
        $filter_name = 'test_filter';
        $request = new lmbHttpRequest('/', 'POST', [], ['id' => 1, 'finish_balance' => 2000, 'other_param1' => 'value1']);
        $toolkit->setRequest($request);

        $filter_helper = new lmbCmsAdminFilterHelper($filter_name);
        $filter_helper->setFilter('id', '');
        $filter_helper->setFilter('login', '');
        $filter_helper->setFilter('role_id', '');
        $filter_helper->setFilter('start_balance', 0);
        $filter_helper->setFilter('finish_balance', 1000);

        $this->assertEquals(['id' => 1, 'login' => '', 'role_id' => '', 'start_balance' => 0, 'finish_balance' => 2000], $filter_helper->getParams());
    }

    function testAfterPostHelperReInitFilter()
    {
        $toolkit = lmbToolkit::instance();
        $filter_name = 'test_filter';
        $request = new lmbHttpRequest('/', 'POST', [], ['id' => 1, 'finish_balance' => 2000, 'other_param1' => 'value1']);
        $toolkit->setRequest($request);

        $filter_helper = new lmbCmsAdminFilterHelper($filter_name);
        $filter_helper->setFilter('id', '');
        $filter_helper->setFilter('login', '');
        $filter_helper->setFilter('role_id', '');
        $filter_helper->setFilter('start_balance', 0);
        $filter_helper->setFilter('finish_balance', 1000);

        $request2 = new lmbHttpRequest('/', 'GET', [], []);
        $toolkit->setRequest($request2);

        $this->assertEquals(['id' => 1, 'login' => '', 'role_id' => '', 'start_balance' => 0, 'finish_balance' => 2000], $filter_helper->getParams());
    }

}
