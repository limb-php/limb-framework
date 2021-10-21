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
if(!isset($_ENV['LIMB_LAZY_CLASS_PATHS']))
  $_ENV['LIMB_LAZY_CLASS_PATHS'] = array();
if(!isset($_ENV['LIMB_LAZY_TRIED']))
  $_ENV['LIMB_LAZY_TRIED'] = array();
define('LIMB_UNDEFINED', 'undefined' . microtime());
define('LIMB_PACKAGES_DIR', dirname(__FILE__) . '/../');

require_once('limb/core/src/assert.inc.php');
require_once('limb/core/src/env.inc.php');
require_once('limb/core/src/string.inc.php');

use limb\core\src\exception\lmbException;
use limb\core\src\exception\lmbInvalidArgumentException;
use limb\core\src\lmbBacktrace;

function lmb_resolve_include_path($path)
{
  if(function_exists('stream_resolve_include_path'))
    return stream_resolve_include_path($path);

  foreach(lmb_get_include_path_items() as $dir)
  {
    $full_path = "$dir/$path";
    if(is_file($full_path) || is_dir($full_path))
      return $full_path;
  }
}

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
  return (($path{0} == '/' || $path{0} == '\\') ||
          (strlen($path) > 2 && $path{1} == ':'));
}

function lmb_require($file_path, $class = '')
{
  if(strpos($file_path, '*') !== false)
  {
    $file_paths = lmb_glob($file_path);
    if(is_array($file_paths))
      foreach($file_paths as $path)
        lmb_require($path);

    return;
  }

  if(!$class)
  {
    //autoguessing class or interface name by file
    $file = basename($file_path);
    $items = explode('.', $file);

    if(isset($items[1]))
    {
      if($items[1] == 'class' || $items[1] == 'interface')
        $class = $items[0];
    }
  }

  if(!include_once($file_path))
  {
    if(class_exists('limb\core\src\exception\lmbException'))
      $exception_class = 'limb\core\src\exception\lmbException';
    else
      $exception_class = 'Exception';
    throw new $exception_class("lmb_require: could not include source file '$file_path'");
  }
}

function lmb_require_glob($file_path)
{
  if(strpos($file_path, '*') !== false)
  {
    foreach(lmb_glob($file_path) as $path)
      lmb_require($path);
  }
  else
    lmb_require($path);
}

function lmb_require_optional($file_path)
{
  if(!file_exists($file_path))
    return;

  lmb_require($file_path);
}

function lmb_autoload($class)
{
  $found = false;

  $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.class.php';

  /*$paths = explode(PATH_SEPARATOR, get_include_path());
  foreach ($paths as $path)
  {
    $fullname = $path . DIRECTORY_SEPARATOR . $file;
    if (file_exists($fullname)) {
      $found = $fullname;
      break;
    }
  }*/

  $fullname = stream_resolve_include_path($file);
  if ($fullname !== false)
  {
    $found = $fullname;
  }

  /*$fullname = $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.class.php';
  $found = true;*/

  if ($found)
  {
    require($fullname);
  }
  else
  {
      $message = "lmb_autoload: could not include source file '$file'";
      if (class_exists('limb\core\src\exception\lmbException') && class_exists('limb\core\src\lmbBacktrace')) {
        $trace = new lmbBacktrace(20, 1);
        throw new lmbException($message, array('trace' => $trace->toString()));
      } else {
        throw new Exception($message);
      }
  }

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

spl_autoload_register('lmb_autoload');