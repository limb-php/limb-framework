<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cms\cases\Toolkit;

use limb\cms\src\Auth\lmbAuth;
use limb\cms\src\model\AuthSessionInterface;
use limb\cms\src\model\lmbCmsSessionUser;
use limb\cms\src\Repository\lmbUserRepository;
use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . './../.setup.php');

class ToolkitTest extends TestCase
{
    protected function tearDown(): void
    {
        lmbToolkit::instance()->getSession()->reset();
    }

    function testGetUserRepository()
    {
        $repository = lmbToolkit::instance()->getUserRepository();

        $this->assertEquals(lmbUserRepository::class, get_class($repository));
        $this->assertEquals(1, $repository->findById(1)->getId());
        $this->assertEquals('admin', $repository->findById(1)->getLogin());
    }

    function testGetCmsAuthSessionNoUser()
    {
        $auth_session = lmbToolkit::instance()->getCmsAuthSession();

        $this->assertInstanceOf(AuthSessionInterface::class, $auth_session);
        $this->assertEquals(null, $auth_session->getUser());
    }

    function testGetCmsAuthSessionWithUser()
    {
        $repository = lmbToolkit::instance()->getUserRepository();
        $auth_session = lmbToolkit::instance()->getCmsAuthSession();

        $user = $repository->findById(1);
        $auth_session->setUser($user);

        $this->assertEquals($user, $auth_session->getUser());
    }

    function testGetCmsAuthSessionWithUser2()
    {
        $repository = lmbToolkit::instance()->getUserRepository();
        $auth_session = lmbToolkit::instance()->getCmsAuthSession();

        $user = $repository->findByLogin('admin');
        $user->getId(); // initialize object
        lmbAuth::loginCredentials('admin', 'secret');

        $this->assertEquals($user, $auth_session->getUser());
        $this->assertTrue($auth_session->isLoggedIn());
    }

    function testGetCmsAuthSessionWithUser3()
    {
        $auth_session = lmbToolkit::instance()->getCmsAuthSession();

        lmbAuth::loginCredentials('admin', 'secret');

        $this->assertTrue($auth_session->isLoggedIn());

        lmbAuth::logout();

        $this->assertFalse($auth_session->isLoggedIn());
    }
}