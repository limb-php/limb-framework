<?php
use limb\fs\src\lmbFs;

if(!function_exists('lmb_tests_init_var_dir'))
{
    function lmb_tests_init_var_dir($value, $echo = false)
    {
        if(file_exists($value))
            lmbFs::rm($value);
        lmbFs::mkdir($value);
        $real = realpath($value).'/';
        lmb_var_dir($real);

        if($echo)
            echo "INFO: Var dir inited in {$real}\n";
    }
}
