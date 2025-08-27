<?php

namespace tests\cms\cases\Fetcher;

use limb\cms\src\Auth\lmbAuth;
use limb\cms\src\fetcher\lmbCmsAdminNavigationFetcher;
use limb\cms\src\model\lmbCmsUserRoles;
use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . '/../.setup.php');

class AdminNavigationFetcherTest extends TestCase
{
    function testNavigationFetcherCreds()
    {
        $toolkit = lmbToolkit::instance();
        $conf = $toolkit->getConf('navigation');

        $admin_nav = (new lmbCmsAdminNavigationFetcher)->fetch();
        $this->assertEquals(null, $admin_nav['title']);

        lmbAuth::loginCredentials('admin', 'secret');

        $admin_nav2 = (new lmbCmsAdminNavigationFetcher)->fetch();
        $this->assertEquals($conf[lmbCmsUserRoles::ADMIN][1], $admin_nav2[1]);
    }

//    function testNavigationFetcher()
//    {
//        $toolkit = lmbToolkit::instance();
//        $conf = $toolkit->getConf('navigation');
//
//        $admin_nav = (new lmbCmsAdminNavigationFetcher)->fetch();
//        $this->assertEquals(null, $admin_nav['title']);
//
//        //lmbAuth::login('admin');
//
//        $admin_nav2 = (new lmbCmsAdminNavigationFetcher)->fetch();
//        $this->assertEquals($conf[lmbCmsUserRoles::ADMIN][1], $admin_nav2[1]);
//    }
}
