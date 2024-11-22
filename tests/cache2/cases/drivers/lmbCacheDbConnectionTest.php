<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheDbConnectionTest extends lmbCacheConnectionTestCase
{
    protected $skip = false;

    function __construct()
    {
        parent::__construct();

        $this->dsn = 'db://dsn?table=lmb_cache2';
    }

    function setUp(): void
    {
        if ($this->skip)
            $this->markTestSkipped('lmbCacheDbConnection test skipped (no fixture found).');

    }

}
