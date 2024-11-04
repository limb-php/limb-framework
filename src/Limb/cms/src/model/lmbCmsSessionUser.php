<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\model;

use limb\active_record\lmbActiveRecord;
use limb\dbal\criteria\lmbSQLFieldCriteria;

/**
 * class lmbCmsClassName.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsSessionUser
{
    protected $user_id;
    protected $is_logged_in = false;
    protected $user;

    function getUser(): lmbCmsUser
    {
        if (is_object($this->user))
            return $this->user;

        if ($this->_isValidSession()) {
            $this->user = lmbActiveRecord::findById(lmbCmsUser::class, $this->user_id);
            if ($this->user)
                $this->user->setLoggedIn($this->is_logged_in);
        }

        if(!$this->user)
            $this->user = new lmbCmsUser();

        return $this->user;
    }

    function setUser($user)
    {
        $this->user = $user;
        $this->user_id = $user->id;
    }

    function login($login, $password)
    {
        $criteria = new lmbSQLFieldCriteria('login', $login);
        $user = lmbActiveRecord::findFirst(lmbCmsUser::class, array('criteria' => $criteria));

        if ($user && $user->isPasswordCorrect($password)) {
            return $this->autoLogin($user);
        }

        $this->setLoggedIn(false);
        return false;
    }

    function autoLogin($user)
    {
        $this->setUser($user);
        $this->setLoggedIn(true);
        return true;
    }

    function logout()
    {
        $this->setLoggedIn(false);
    }

    function isLoggedIn()
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
