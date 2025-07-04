<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\session\src\toolkit;

use limb\core\src\exception\lmbException;
use limb\dbal\src\toolkit\lmbDbTools;
use limb\session\src\lmbSessionStorageInterface;
use limb\toolkit\src\lmbAbstractTools;
use limb\session\src\lmbSession;
/**
 * class lmbSessionTools.
 *
 * @package session
 * @version $Id: lmbSessionTools.php 8176 2022-04-23 16:41:47Z
 */
class lmbSessionTools extends lmbAbstractTools
{
    protected $session;

    protected $session_drivers_dict = [];
    protected $session_drivers = [];

    static function getRequiredTools()
    {
        return [
            lmbDbTools::class
        ];
    }

    function getSession(): lmbSession
    {
        if (is_object($this->session))
            return $this->session;

        $this->session = new lmbSession();

        return $this->session;
    }

    function setSession($session)
    {
        $this->session = $session;
    }


    function sessionStorageFactory($session_type, $options = []): lmbSessionStorageInterface
    {
        if(!isset($this->session_drivers_dict[$session_type]))
            throw new lmbException('Session storage "' . $session_type . '" not found');

        if(isset($this->session_drivers[$session_type]))
            return $this->session_drivers[$session_type];

        $storageInitClass = $this->session_drivers_dict[$session_type];
        return $this->session_drivers[$session_type] = $storageInitClass::init($options);
    }

    function registerSessionStorageDriver($session_type, $storage_initializer_class): void
    {
        $this->session_drivers_dict[$session_type] = $storage_initializer_class;
    }

}
