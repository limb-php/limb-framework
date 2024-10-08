<?php
set_include_path(dirname(__FILE__) . '/../../../../');
require('limb/core/common.inc.php');

use limb\fs\lmbFs;

/*--------------------------------------*/
lmbFs::mkDir(dirname(__FILE__) . '/temp/');

generateBundle('cc');

$mark = microtime(true);

require_once(dirname(__FILE__) . '/temp/bundle.inc.php');

for ($i = 0; $i < 300; $i++) {
    $class_name = 'MyClass' . $i . 'cc';
    $object = new $class_name();
}

echo "require_once absolute: " . (microtime(true) - $mark) . "\n";

/*--------------------------------------*/
generateFiles('aa');

$mark = microtime(true);

$dir = dirname(__FILE__) . '/temp/';
for ($i = 0; $i < 300; $i++) {
    $class_name = 'MyClass' . $i . 'aa';
    $object = new $class_name();
}

echo "require $i files: " . (microtime(true) - $mark) . "\n";

lmbFs::rm(dirname(__FILE__) . '/temp/');

/*--------------------------------------*/

function generateBundle($sufffix)
{
    $bundle = "";

    for ($i = 0; $i < 300; $i++) {
        $content = getContent($i . $sufffix);
        $bundle .= $content;
    }

    file_put_contents(dirname(__FILE__) . '/temp/bundle.inc.php', '<?php ' . $bundle . ' ?>');
}

function generateFiles($sufffix)
{
    for ($i = 0; $i < 300; $i++) {
        $content = getContent($i . $sufffix);
        file_put_contents(dirname(__FILE__) . '/temp/MyClass' . $i . $sufffix . '.php', '<?php ' . $content . ' ?>');
    }
}

function getContent($sufffix)
{
    $content = 'class MyClass' . $sufffix;
    $content .= file_get_contents(dirname(__FILE__) . '/class_content.inc');
    return $content;
}

