<?php
set_include_path(dirname(__FILE__) . '/src/' . PATH_SEPARATOR . get_include_path());

function lmb_autoload($className)
{
  $className = ltrim($className, '\\');
  $fileName  = '';
  $namespace = '';
  if ($lastNsPos = strrpos($className, '\\')) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

  $fullname = stream_resolve_include_path($fileName);
  if ($fullname !== false)
  {
    require($fullname);
  }
}

spl_autoload_register('lmb_autoload');