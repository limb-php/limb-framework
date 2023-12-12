<?php

set_include_path(dirname(__FILE__) . '/../../../../');

require_once('limb/core/common.inc.php');

use limb\macro\src\lmbMacroTemplate;

for ($i = 0; $i < 1000; $i++)
    $j = $i;

$name = 'Bob';

$tpl = new lmbMacroTemplate('macro.phtml',
    array('cache_dir' => '/tmp/macro',
        'forcecompile' => false,
        'forcescan' => false,
        'tpl_scan_dirs' => array(dirname(__FILE__) . '/tpl')));
$tpl->set('name', $name);

$mark = microtime(true);

$tpl->render();

echo "MACRO 1xrender: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    $tpl->render();
}

echo "MACRO 1000xrender: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

ob_start();
include(dirname(__FILE__) . '/tpl/native.phtml');
ob_get_contents();
ob_end_clean();

echo "PHP 1xrender: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    ob_start();
    include(dirname(__FILE__) . '/tpl/native.phtml');
    ob_get_contents();
    ob_end_clean();
}

echo "PHP 1000xrender: " . (microtime(true) - $mark) . "\n";
