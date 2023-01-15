<?php
set_include_path(dirname(__FILE__) . '/src/' . PATH_SEPARATOR . get_include_path());

$_ENV['LIMB_START_TIME'] = microtime(true);

function lmb_autoload($className)
{
  $basedir = '';

  $className = ltrim($className, '\\');
  $fileName  = '';
  $namespace = '';
  if ($lastNsPos = strrpos($className, '\\')) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

  $fullname = stream_resolve_include_path($basedir . $fileName);
  if ($fullname !== false)
  {
    require_once($fullname);
  }
}

if( defined('LIMB_AUTOLOAD_CLASSES') && LIMB_AUTOLOAD_CLASSES )
  spl_autoload_register('lmb_autoload');