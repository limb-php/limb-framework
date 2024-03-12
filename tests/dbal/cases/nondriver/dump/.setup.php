<?php

use limb\toolkit\src\lmbToolkit;

lmbToolkit::instance()->setDefaultDbDSN(lmbToolkit::instance()->getDbDSNByName('mysql_dsn'));
