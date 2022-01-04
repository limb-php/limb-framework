<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\core\src;

use limb\core\src\lmbHandle;
use limb\core\src\exception\lmbException;

/**
 * class lmbClassPath.
 *
 * @package core
 * @version $Id$
 */
class lmbClassPath
{
  protected $class_name;
  protected $raw_path;

  function __construct($raw_path)
  {
    if(is_string($raw_path))
      $this->raw_path = $this->_parseConstants($raw_path);

    $this->_initClassName();
  }

  function getClassName()
  {
    return $this->class_name;
  }

  static function create($path, $args = array())
  {
    $class_path = new lmbClassPath($path);
    return $class_path->createObject($args);
  }

  function import()
  {
    if(!class_exists($this->class_name))
    {
      throw new lmbException("Could not import class '{$this->class_name}'");
    }
  }

  function createHandle($args = array())
  {
    return new lmbHandle($this->raw_path, $args);
  }

  function createObject($args = array())
  {
    $refl = new \ReflectionClass($this->class_name);
    return call_user_func_array(array($refl, 'newInstance'),$args);
  }

  protected function _parseConstants($value)
  {
    //$value = preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $value);
    $value = preg_replace_callback(
        "/([a-z]*)/",
        function($matches){
            foreach($matches as $match){
                return $match;
            }
        },
        $value
    );

    return $value;
  }

  protected function _initClassName()
  {
    if(!$this->raw_path)
      throw new lmbException("Invalid class path: {$this->raw_path}");

    $exp_raw_path = explode('/', $this->raw_path);
    $this->class_name = end($exp_raw_path);
  }
}


