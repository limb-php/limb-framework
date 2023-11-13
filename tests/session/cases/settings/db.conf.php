<?php

return [
    'dsn' => 'mysql://root:test@localhost:3306/tests_limb?charset=utf8',
    'mysql_dsn' => 'mysql://root:test@localhost:3306/tests_limb?charset=utf8',
    'sqlite_dsn' => 'sqlite://localhost/' . lmb_var_dir() . 'sqlite_tests.db',
    'pgsql_dsn' => 'pgsql://postgres:test@localhost:5432/tests_limb?charset=utf8',
];
