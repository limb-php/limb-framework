<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\core\src;

/**
 * class lmbHandle.
 *
 * @package core
 * @version $Id$
 */
class lmbHandle extends lmbProxy
{
  protected $class;
  protected $args;

  function __construct($class, ...$args)
  {
      $this->class = $class;

      if( count($args) > 1 ) {
          $this->args = $args;
      }
      else {
          $this->args = $args[0] ?? array();

          if( !is_array($this->args) )
              $this->args = array($this->args);
      }
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
      $refl = new \ReflectionClass($this->class);
      return call_user_func_array(array($refl, 'newInstance'), $this->args);
  }
}
