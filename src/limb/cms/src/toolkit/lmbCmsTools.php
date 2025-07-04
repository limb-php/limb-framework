<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\toolkit;

use limb\cms\src\Auth\AuthenticatableInterface;
use limb\cms\src\Repository\lmbUserRepository;
use limb\cms\src\Repository\lmbUserRepositoryInterface;
use limb\toolkit\src\lmbAbstractTools;
use limb\tree\src\lmbMPTree;
use limb\cms\src\model\lmbCmsSessionUser;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\web_app\src\toolkit\lmbProfileTools;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbCmsTools.
 *
 * @package cms
 * @version $Id: lmbCmsTools.php 7619 2009-02-10 15:07:35Z
 */
class lmbCmsTools extends lmbAbstractTools
{
    protected $user_session_name = 'lmbCmsSessionUser';

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

    function getCmsAuthSession(): lmbCmsSessionUser
    {
        $session = lmbToolkit::instance()->getSession();
        $session_class_name = $this->getUserSessionClassName();

        $session_user = $session->get($session_class_name);
        if (!is_a($session_user, $session_class_name)) {
            $session_user = new $session_class_name( $this->getUserRepository() );
            $session->set($session_class_name, $session_user);
        }

        return $session_user;
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
        $this->setCmsUser(null);
        $session = lmbToolkit::instance()->getSession();
        $session->destroy($this->getUserSessionClassName());
    }

    function setCmsUser($user): void
    {
        $this->user = $user;
    }
}
