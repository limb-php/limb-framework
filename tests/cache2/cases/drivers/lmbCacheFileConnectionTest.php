<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheFileConnectionTest extends lmbCacheConnectionTestCase
{
    function __construct()
    {
        parent::__construct();

        $this->dsn = 'file:///' . lmb_var_dir() . '/cache2';
    }
}
