<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\core\src;

use limb\core\src\lmbProxy;

/**
 * class lmbHandle.
 *
 * @package core
 * @version $Id$
 */
class lmbHandle extends lmbProxy
{
  protected $args;
  protected $class_path;
  protected $class;

  function __construct($class_path, $args = array(), $class = null)
  {
    if(is_null($class))
      $this->class = $this->_getClassName($class_path);
    else
      $this->class = $class;

    $this->class_path = $class_path;
    $this->args = $args;
  }

  function isHandle()
  {
    return true;
  }

  function getClass()
  {
    return $this->class;
  }

  protected function _createOriginalObject()
  {
    //require_once($this->_getClassFullPath());

    $refl = new \ReflectionClass($this->class);
    return call_user_func_array(array($refl, 'newInstance'),$this->args);
  }

  protected function _getClassName($class_path)
  {
    $decoded_class_path = explode('/', $class_path);
    $items = explode('.', end($decoded_class_path));

    return $items[0];
  }

  /*protected function _getClassNamespace($class_path)
  {
    $classname = $this->_getClassName($class_path);
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $classname);

    return $file;
  }*/

  protected function _getClassFullPath()
  {
    if(substr($this->class_path, -4, 4) != '.php')
      return $this->class_path . '.class.php';
    else
      return $this->class_path;
  }
}

