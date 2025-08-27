<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\model;

use limb\cms\src\Auth\AuthenticatableInterface;
use limb\cms\src\Repository\lmbUserRepositoryInterface;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbCmsClassName.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsSessionUser implements AuthSessionInterface
{
    protected $user_id = null;
    protected $user = null;
    protected $is_logged_in = false;

    protected $provider;

    function __construct(lmbUserRepositoryInterface $provider)
    {
        $this->provider = $provider;
        //$this->session = lmbToolkit::instance()->getSession();
    }

    function getUserId()
    {
        return $this->user_id;
    }

    function getUser(): AuthenticatableInterface|null
    {
        if (is_object($this->user))
            return $this->user;

        if ($this->_isValidSession()) {
            $this->user = $this->provider->findById($this->user_id);
        }

        return $this->user;
    }

    function setUser(AuthenticatableInterface $user)
    {
        $this->user = $user;
        $this->user_id = $user->id;
    }

    function resetUser()
    {
        $this->user = null;
        $this->user_id = null;
        $this->is_logged_in = false;
    }

    function login(AuthenticatableInterface $user)
    {
        $this->setUser($user);
        $this->setLoggedIn(true);
        return true;
    }

    function logout()
    {
        $this->setLoggedIn(false);
    }

    function isLoggedIn(): bool
    {
        return $this->is_logged_in;
    }

    function setLoggedIn($logged_in)
    {
        $this->is_logged_in = $logged_in;
    }

    protected function _isValidSession(): bool
    {
        return (isset($this->user_id) && is_integer($this->user_id) && ($this->is_logged_in === true));
    }

    function __sleep()
    {
        return array('user_id', 'is_logged_in');
    }
}
