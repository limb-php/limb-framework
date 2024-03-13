<?php

$dsn_name = 'mysql_dsn';

lmb_tests_init_db_dsn($dsn_name);

lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');
