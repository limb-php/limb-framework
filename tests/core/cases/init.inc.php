<?php
use limb\fs\src\lmbFs;

function lmb_tests_init_var_dir($value)
{
  if(file_exists($value))
      lmbFs::rm($value);
  lmbFs::mkdir($value);
  $real = realpath($value).'/';
  lmb_var_dir($real);
  echo "INFO: Var dir inited in {$real}\n";
}
