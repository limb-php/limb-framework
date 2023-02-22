<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\session\src\toolkit;

use limb\dbal\src\toolkit\lmbDbTools;
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

  static function getRequiredTools()
  {
    return [
      lmbDbTools::class
    ];
  }

  function getSession()
  {
    if(is_object($this->session))
      return $this->session;

    $this->session = new lmbSession();

    return $this->session;
  }

  function setSession($session)
  {
    $this->session = $session;
  }
}
