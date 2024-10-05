<?php

use limb\dbal\lmbDBAL;

set_include_path(dirname(__FILE__) . '/../../../../');

$mark = microtime(true);

require_once('limb/core/common.inc.php');
require_once('limb/dbal/common.inc.php');

echo "dbal common includes: " . (microtime(true) - $mark) . "\n";

$mysql_db = mysqli_connect('localhost', 'gfu', '1561611');
mysqli_select_db('gfu');

mysqli_query('CREATE TABLE foo (bar varchar(10))');
mysqli_query('INSERT INTO foo VALUES (\'some value\')');


$mark = microtime(true);


for ($i = 0; $i < 1000; $i++) {
    $q = mysqli_query('SELECT bar FROM foo');
    while ($entry = mysqli_fetch_assoc($q))
        $bar = $entry['bar'];
}

echo "native mysql fetching: " . (microtime(true) - $mark) . "\n";

$conn = lmbDBAL::newConnection('mysql://gfu:1561611@localhost/gfu');

$mark = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    $rs = lmbDBAL::fetch('SELECT bar FROM foo', $conn);
    foreach ($rs as $record)
        $bar = $record['bar'];
    //$rs->freeQuery();
}

echo "lmbDBAL::fetch(), array access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    $rs = lmbDBAL::fetch('SELECT bar FROM foo', $conn);
    foreach ($rs as $record)
        $bar = $record->get('bar');
}

echo "lmbDBAL::fetch(), getter: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for ($i = 0; $i < 1000; $i++) {
    $stmt = $conn->newStatement('SELECT bar FROM foo');
    $rs = $stmt->getRecordSet();
    foreach ($rs as $record)
        $bar = $record->get('bar');
}

echo "lmbMysqlConnection::newStatement(), getter: " . (microtime(true) - $mark) . "\n";


mysqli_query("drop table foo");

mysqli_close();
