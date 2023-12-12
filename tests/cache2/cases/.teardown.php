<?php

use limb\fs\src\lmbFs;
use limb\fs\src\exception\lmbFsException;

try {
    lmbFs::rm(lmb_var_dir());
} catch (lmbFsException $e) {
}