<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\session\src;

/**
 * lmbSessionNativeStorage does nothing thus keeping native file-based php session storage to be used.
 * @see lmbSessionStartupFilter
 * @version $Id: lmbSessionNativeStorage.php 7486 2009-01-26 19:13:20Z
 * @package session
 */
class lmbSessionNativeStorage implements lmbSessionStorageInterface
{
  /**
   * Does nothing
   * @see lmbSessionStorage::install()
   */
  function install(): bool
  {
      return true;
  }
}
