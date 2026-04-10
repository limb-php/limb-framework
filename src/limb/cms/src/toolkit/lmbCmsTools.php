<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\toolkit;

use limb\cms\src\Auth\AuthenticatableInterface;
use limb\cms\src\Auth\AuthSessionInterface;
use limb\cms\src\Auth\lmbCmsSessionUser;
use limb\cms\src\Repository\lmbUserRepository;
use limb\cms\src\Repository\lmbUserRepositoryInterface;
use limb\toolkit\src\lmbAbstractTools;
use limb\toolkit\src\lmbToolkit;
use limb\tree\src\lmbMPTree;
use limb\web_app\src\toolkit\lmbProfileTools;
use limb\web_app\src\toolkit\lmbWebAppTools;

/**
 * class lmbCmsTools.
 *
 * @package cms
 * @version $Id: lmbCmsTools.php 7619 2009-02-10 15:07:35Z
 */
class lmbCmsTools extends lmbAbstractTools
{
    protected $tree;
    protected $user;

    static function getRequiredTools()
    {
        return [
            lmbWebAppTools::class,
            lmbProfileTools::class
        ];
    }

    function getCmsTree($tree_name = 'node')
    {
        if (isset($this->tree[$tree_name]) && is_object($this->tree[$tree_name]))
            return $this->tree[$tree_name];

        $this->tree[$tree_name] = new lmbMPTree($tree_name);

        return $this->tree[$tree_name];
    }

    function setCmsTree($tree)
    {
        $this->tree = $tree;
    }

    /* user */
    function getUserSessionClassName(): string
    {
        return lmbCmsSessionUser::class;
    }

    function getUserRepository(): lmbUserRepositoryInterface
    {
        return lmbUserRepository::factory();
    }

    function getCmsAuthSession(): AuthSessionInterface
    {
        $session = lmbToolkit::instance()->getSession();
        $session_class_name = $this->getUserSessionClassName();

        $session_user = $session->get($session_class_name);
        if (!is_a($session_user, $session_class_name)) {
            $session_user = new $session_class_name( lmbToolkit::instance()->getUserRepository() );
            $session->set($session_class_name, $session_user);
        }

        return $session_user;
    }

    function resetCmsAuthSession()
    {
        $session = lmbToolkit::instance()->getSession();
        $session->destroy($this->getUserSessionClassName());
    }

    function getCmsUser(): AuthenticatableInterface|null
    {
        if (is_object($this->user))
            return $this->user;

        $session_user = lmbToolkit::instance()->getCmsAuthSession();

        return $this->user = $session_user->getUser();
    }

    function resetCmsUser(): void
    {
        $session_user = lmbToolkit::instance()->getCmsAuthSession();
        $session_user->resetUser();

        $this->setCmsUser(null);
    }

    function setCmsUser($user): void
    {
        $this->user = $user;
    }
}
