<?php
set_include_path(dirname(__FILE__) . '/../../../../' . PATH_SEPARATOR. '.');
require('limb/core/common.inc.php');

$path = dirname(__FILE__) . '/MyClass.php';

$mark = microtime(true);

echo "lmb_require same class, no autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

echo "lmb_require unique class, no autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

$object = new MyClass();

echo "lmb_require absolute, same class, autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

$object = new MyClass();

echo "lmb_require relative, same class, autoload: " . (microtime(true) - $mark) . "\n";

for($i=0;$i<1000;$i++)
{
  make_class('UniqueClass' . $i);
}

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClass' . $i;
  $object = new $class;
}

echo "lmb_require absolute, unique class, autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClass' . $i;
  $object = new $class;
}

echo "lmb_require absolute, again: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  require_once($path);
}
$object = new MyClass();

echo "require_once absolute, same class: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  require_once('MyClass.php');
}
$object = new MyClass();

echo "require_once relative, same class: " . (microtime(true) - $mark) . "\n";

for($i=0;$i<1000;$i++)
{
  make_class('UniqueClazz' . $i);
}

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClazz' . $i;
  require_once(dirname(__FILE__) . '/tmp/' . $class . '.php');
  $object = new $class();
}

echo "require_once absolute, unique class: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClazz' . $i;
  require_once(dirname(__FILE__) . '/tmp/' . $class . '.php');
  $object = new $class();
}

echo "require_once absolute, again: " . (microtime(true) - $mark) . "\n";

`rm -rf ./tmp`;

function make_class($name)
{
  if(!is_dir('./tmp'))
    mkdir('./tmp');
  file_put_contents('./tmp/' . $name . '.php',
                    '<?php class ' . $name . ' {}; ?>');
}
