<?php

use limb\fs\src\lmbFs;

if (!function_exists('lmb_tests_init_var_dir')) {
    function lmb_tests_init_var_dir($value, $verbose = false): void
    {
        if (file_exists($value))
            lmbFs::rm($value);
        lmbFs::mkdir($value);
        $real = realpath($value) . DIRECTORY_SEPARATOR;
        lmb_var_dir($real);

        if ($verbose)
            echo "INFO: Var dir sets to {$real}\n";
    }
}
