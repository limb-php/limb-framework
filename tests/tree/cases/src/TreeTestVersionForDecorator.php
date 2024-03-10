<?php

namespace tests\tree\cases\src;

use limb\tree\src\lmbMPTree;

class TreeTestVersionForDecorator extends lmbMPTree
{
    function __construct()
    {
        parent::__construct('test_materialized_path_tree');
    }
}
