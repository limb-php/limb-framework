<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2020 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\session\src;

use limb\core\src\lmbSerializable;

/**
 * Wrapper class for global $_SESSION variable
 *
 * @see lmbWebAppTools::getSession()
 * @version $Id: lmbSession.php 8111 2010-01-28 17:19:42Z
 * @package session
 */
class lmbSession implements \ArrayAccess,\Iterator,\Countable
{
  /**
   * @var array variables names that were changed. Used for testing purposes mostly.
   */
  protected $touched_names = array();

  /**
   * Starts session and installs driver
   * @param lmbSessionStorageInterface $storage Concrete session driver
   */
  function start(lmbSessionStorageInterface $storage = null)
  {
    if($storage)
      $storage->install();
    return session_start();
  }

  function close()
  {
    return session_write_close();
  }

  /**
   * Register a variable in session and returns a reference to it.
   * @param string $name variable name
   */
  function & registerReference($name)
  {
    if(!isset($_SESSION[$name]))
      $_SESSION[$name] = '';

    $this->touched_names[$name] = true;
    return $_SESSION[$name];
  }

  /**
   * Returns variable value from session
   * @param string $name variable name
   * @param mixed $empty_value value that should be returned if variable is not registered in session yet
   * @return mixed
   */
  function get($name, $empty_value = null)
  {
    if(!isset($_SESSION[$name]))
      return $empty_value;

    if($_SESSION[$name] instanceof lmbSerializable)
      return $_SESSION[$name]->getSubject();
    else
      return $_SESSION[$name];
  }

  /**
   * Returns value from session by key
   * @param string $key variable name
   * @return mixed
   */
  function __get($key)
  {
    return $this->get($key);
  }

  /**
   * Sets variable into session
   * Automatically wraps objects with {@link lmbSerializable}
   * that helps to prevent <b>"class is not defined"</b> error while restoring session
   * @param string $name variable name
   * @param mixed $value value
   * @return void
   */
  function set($name, $value)
  {
    if(is_object($value) && !($value instanceof lmbSerializable))
      $_SESSION[$name] = new lmbSerializable($value);
    else
      $_SESSION[$name] = $value;

    $this->touched_names[$name] = true;
  }

  /**
   * Sets variable into session
   * @param string $key variable name
   * @param mixed $value value
   * @return void
   */
  function __set($key, $value)
  {
    $this->set($key, $value);
  }

  /**
   * Clears session
   * @return void
   */
  function reset()
  {
    $_SESSION = array();
  }

  /**
   * Returns TRUE if session has such variable
   * @param string $name variable name
   * @return boolean
   */
  function exists($name)
  {
    return isset($_SESSION[$name]);
  }

  /**
   * Removes variable from session
   * @param string $name variable name
   * @return void
   */
  function destroy($name)
  {
    if(isset($_SESSION[$name]))
      unset($_SESSION[$name]);
  }

  /**
   * Alias for destroy() method
   * @see destroy()
   * @return void
   */
  function remove($name)
  {
    $this->destroy($name);
  }

  /**
   * Returns session data
   * @return array
   */
  function export()
  {
    return $_SESSION;
  }

  /**
   * Removed all variables that were changed
   * @see $touched_names
   * @return void
   */
  function destroyTouched()
  {
    foreach(array_keys($this->touched_names) as $name)
      $this->destroy($name);
  }

  /**
   * Dumps session data (for debugging purposes mostly)
   * @return mixed
   */
  function dump()
  {
    $str = "";
    if(!isset($_SESSION) || !is_array($_SESSION))
      return $str;

    foreach($_SESSION as $key => $value)
    {
      $str .= "$key : ";
      if(is_array($value))
        $str .= 'ARRAY[' . sizeof($value) . ']';
      elseif(is_object($value))
        $str .= 'OBJECT(' . get_class($value) . ')';
      else
        $str .= strtoupper(gettype($value));
      $str .= "\n";
    }
    return $str;
  }
  /**
   * Removes variable from session
   * Alias for destroy() method
   * @see destroy()
   * @param string $offset
   * @return void
   */
  function offsetUnset($offset): void
  {
    $this->destroy($offset);
  }

  function offsetExists($offset): bool
  {
    return $this->exists($offset);
  }

  function offsetGet($offset)
  {
    return $this->get($offset);
  }

  function offsetSet($offset,$value): void
  {
    $this->set($offset, $value);
  }

  function current()
  {
    return current($_SESSION);
  }

  function next(): void
  {
    next($_SESSION);
  }

  function key()
  {
    return key($_SESSION);
  }

  function valid(): bool
  {
    return !is_null($this->key());
  }

  function rewind(): void
  {
    reset($_SESSION);
  }

  function count(): int
  {
    return count($_SESSION);
  }
}
