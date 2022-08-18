<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package core
 * @version $Id: common.inc.php 8127 2010-02-10 10:40:35Z conf $
 */
define('LIMB_UNDEFINED', 'undefined' . microtime());
define('LIMB_PACKAGES_DIR', dirname(__FILE__) . '/../');

require_once(dirname(__FILE__) . '/src/assert.inc.php');
require_once(dirname(__FILE__) . '/src/env.inc.php');
require_once(dirname(__FILE__) . '/src/string.inc.php');

function lmb_glob($path)
{
  if(lmb_is_path_absolute($path))
    return glob($path);

  $result = array();
  foreach(lmb_get_include_path_items() as $dir)
  {
    if($res = glob("$dir/$path"))
    {
      foreach($res as $item)
         $result[] = $item;
    }
  }
  return $result;
}

function lmb_get_include_path_items()
{
  return explode(PATH_SEPARATOR, get_include_path());
}

function lmb_is_path_absolute($path)
{
  if(!$path)
    return false;

  //very trivial check, is more comprehensive one required?
  return (($path[0] == '/' || $path[0] == '\\') ||
          (strlen($path) > 2 && $path[1] == ':'));
}

function lmb_var_dump($obj, $echo = false)
{
  ob_start();
  var_dump($obj);
  $dump = ob_get_contents();
  ob_end_clean();

  if($echo)
  {
    if(PHP_SAPI != 'cli')
    {
      echo '<pre>';
      echo $dump;
      echo '</pre>';
    }
    else
      echo $dump;
  }
  else
    return $dump;
}

function lmb_var_dir($value = null)
{
  if($value)
    lmb_env_set('LIMB_VAR_DIR', $value);
  else
    return lmb_env_get('LIMB_VAR_DIR');
}
