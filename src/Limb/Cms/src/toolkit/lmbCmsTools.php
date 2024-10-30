<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\toolkit;

use limb\toolkit\lmbAbstractTools;
use limb\tree\lmbMPTree;
use limb\cms\src\model\lmbCmsSessionUser;
use limb\web_app\src\toolkit\lmbWebAppTools;
use limb\web_app\src\toolkit\lmbProfileTools;
use limb\toolkit\lmbToolkit;

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
    function getUserSessionName(): string
    {
        return $this->user_session_name;
    }

    function getCmsAuthSession(): lmbCmsSessionUser
    {
        $session = lmbToolkit::instance()->getSession();
        $session_user = $session->get($this->getUserSessionName());
        if (!is_a($session_user, lmbCmsSessionUser::class)) {
            $session_user = new lmbCmsSessionUser();
            $session->set($this->getUserSessionName(), $session_user);
        }

        return $session_user;
    }

    function getCmsUser()
    {
        if (is_object($this->user))
            return $this->user;

        $session_user = $this->toolkit->getCmsAuthSession();

        return $this->user = $session_user->getUser();
    }

    function resetCmsUser(): void
    {
        $this->setCmsUser(null);
        $session = lmbToolkit::instance()->getSession();
        $session->destroy($this->getUserSessionName());
    }

    function setCmsUser($user): void
    {
        $this->user = $user;
    }
}
