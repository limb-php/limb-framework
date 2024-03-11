<?php

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/active_record/');

lmb_tests_init_db_dsn();

lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');
